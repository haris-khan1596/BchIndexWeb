<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use PDF;


class UserTransferMoneyController extends Controller
{
    public function transfer($transactions_id='')
    {
        $pageTitle = 'P2P Transfer';
        $wallets = Wallet::where('user_id', auth()->id())->with('crypto')->latest()->get();
        $walletsAmounts = $this->calculateUserWalletsRatesInOwnAndUSD($wallets);
        return view($this->activeTemplate . 'user.transfer_money', compact('pageTitle','transactions_id', 'wallets', 'walletsAmounts'));
    }

    public function userExist()
    {
        $otherUser = User::where('username', request()->userName)->orWhere('email', request()->userName)->first();
        $user = auth()->user();
        if (!$otherUser) {
            return response()->json(['success' => false, 'msg' => "User doesn't exists."]);
        }
        if ($otherUser && $user->username == $otherUser->username || $user->email == $otherUser->email) {
            return response()->json(['success' => false, 'msg' => 'Can\'t transfer/request to your own']);
        }
        return response()->json(['success' => true, 'user' => $otherUser]);
    }

    public function transferMoney()
    {
        request()->validate([
            'otherUser' => 'required',
            'wallet_id' => 'required',
            'amount' => 'required'
        ]);
        try {
            $notify = DB::transaction(function () {
                $selectedWallet = Wallet::where('id', request()->wallet_id)->with('crypto')->first();

                //$modefiedAmount = (float)(1 / $selectedWallet->crypto->rate) * request()->amount;

                $amount = request()->amount;

                $otherUserWallet = Wallet::where('user_id', request()->otherUser)->where('crypto_currency_id', $selectedWallet->crypto_currency_id)->first();

                if ($amount > $selectedWallet->balance) {
                    $notify[] = ['error', 'Amount must be between 0 and ' . request()->amount];
                    return $notify;
                }
                if (empty($otherUserWallet)) {
                    $notify[] = ['error', 'Selected User can not receive this Wallet'];
                    return $notify;
                }

                $modefiedAmount = showAmount($amount/$selectedWallet->crypto->rate, 6);

                //
                $otherUser = User::where('id', $otherUserWallet->user_id)->first();
                $user = auth()->user();
                $transaction_details = 'P2P Transfer Amount '.$selectedWallet->crypto->code.' '.$modefiedAmount. ' And USD '.$amount.' From '.$user->email.' To '.$otherUser->email.'';

                //dd($modefiedAmount,$amount,$transaction_details);

                $selectedWallet->balance = $selectedWallet->balance - $amount;
                $selectedWallet->save();
                $otherUserWallet->balance = $otherUserWallet->balance + $amount;
                $otherUserWallet->save();


                $transactions = [
                    'user_id' => $selectedWallet->user_id,
                    'crypto_currency_id' => $selectedWallet->crypto->id,
                    'amount' => $amount,
                    'post_balance' => $selectedWallet->balance,
                    'charge' => 0,
                    'trx_type' => '-',
                    'details' => $transaction_details,
                    'remark' => 'p2p_transfer|'.$otherUser->id.'',
                    'trx' => getTrx(),
                    'created_at' => now()
                ];

                $transaction_details = 'P2P Transfer Amount '.$otherUserWallet->crypto->code.' '.$modefiedAmount. ' And USD '.$amount.' From '.$otherUser->email.' To '.$user->email.'';

                $transactions_to = [
                    'user_id' => $otherUserWallet->user_id,
                    'crypto_currency_id' => $selectedWallet->crypto->id,
                    'amount' => $amount,
                    'post_balance' => $otherUserWallet->balance,
                    'charge' => 0,
                    'trx_type' => '+',
                    'details' => $transaction_details,
                    'remark' => 'p2p_transfer|'.$user->id.'',
                    'trx' => getTrx(),
                    'created_at' => now()
                ];

                if (isset($transactions) && isset($transactions_to)) {
                    //$res = Transaction::create($transactions);
                    $res_id = Transaction::insertGetId($transactions);
                    $res_id_to = Transaction::insertGetId($transactions_to);
                }
                $notify[] = ['success', 'Transferred successfully!',$res_id];
                return $notify;
            });
            return redirect()->route('user.transfer',$notify[0][2])->withNotify($notify);
            //return back()->withNotify($notify);
        } catch (\Throwable $th) {
            $notify[] = ['error', 'Something went wrong!'];
            return back()->withNotify($notify);
        }
    }

    private function calculateUserWalletsRatesInOwnAndUSD($wallets)
    {
        $walletsAmounts = [];
        foreach ($wallets as $wallet) {
            $walletAmount = [];
            $walletAmount['id'] = $wallet->id;
            $walletAmount['rate'] = $wallet->crypto->rate;
            $walletAmount['code'] = $wallet->crypto->code;
            
            if($wallet->crypto->rate==0){
                $wallet->crypto->rate = 1;
            }
            
            $walletAmount['own_balance'] = number_format($wallet->balance / $wallet->crypto->rate, 6);// . " " . $wallet->crypto->code;
            $walletAmount['usd_balance'] = number_format($wallet->balance, 2) . " USD";
            $walletsAmounts[] = $walletAmount;
        }
        return $walletsAmounts;
    }

    //p2p_reciept
    public function p2p_receipt($id,$ajax = false)
    {
        $pageTitle = 'P2P Transfer Receipt';

        $transaction = Transaction::where('user_id', auth()->id())->where('id','=',$id)->with('crypto')->first();

        $type_otherUserid = explode('|',$transaction->remark);

        $otherUser = User::where('id', $type_otherUserid[1])->first();

        $user = auth()->user();

        $selectedWallet = Wallet::where('crypto_currency_id', $transaction->crypto_currency_id)->where('user_id', $user->id)->with('crypto')->first();
        
        $modefiedAmount = showAmount($transaction->amount/$selectedWallet->crypto->rate, 6);

        $transaction->amount = $selectedWallet->crypto->code.' '. $modefiedAmount.' And USD '.$transaction->amount;

        if($ajax==true) {
            return view($this->activeTemplate . 'user.p2p_receipt', compact('pageTitle', 'transaction', 'user', 'otherUser'));
        }else {
            $pdf = PDF::loadView($this->activeTemplate . 'user.p2p_receipt', compact('pageTitle', 'transaction', 'user', 'otherUser'));
            return $pdf->stream($pageTitle . '.pdf');
        }
    }
}
