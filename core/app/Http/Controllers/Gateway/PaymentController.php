<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Models\CryptoCurrency;
use App\Models\Wallet;
use App\Models\spotWallet;
use Illuminate\Http\Request;

use App\Http\Controllers\Gateway\Coinpayments\CoinPaymentHosted;
use App\Models\AdminNotification;
use App\Models\Admin;
use App\Models\CryptoWallet;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\TransactionCharge;
use App\Models\CoinPair;

class PaymentController extends Controller
{
    public function __construct()
    {
        return $this->activeTemplate = activeTemplate();
    }


    public function wallets()
    {
        $pageTitle = 'Your Receiving Wallets';
        $wallets = Wallet::where('user_id', auth()->id())->with('crypto')->get();
        //$cryptoWallets = CryptoWallet::where('user_id', auth()->id())->latest()->with('crypto')->paginate(getPaginate());
        $cryptoWallets = [];
        return view($this->activeTemplate . 'user.wallet', compact('pageTitle', 'wallets', 'cryptoWallets'));
    }



    public function transfer_p2s(Request $request , string $Cid, string $Uid)
    {
        $cryptoCurr = CryptoCurrency::where('id', $Cid)->first();
        // Fetch Spot wallet where user id = $Uid and crypto id = $Cid
        $wallets = spotWallet::where('user_id', $Uid)->where('crypto_currency_id', $Cid)->get();
        $pageTitle = 'Transfer - Spot to P2P';
        $transaction_charges=TransactionCharge::where('id','10')->first()->percent_charge;
        return view($this->activeTemplate . 'user.transfer_p2s', compact('pageTitle','cryptoCurr','wallets','Uid','Cid', 'transaction_charges'));
    }

    public function transfer_p2s_post(Request $request , string $Cid, string $Uid)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);


        $cryptoCurr = CryptoCurrency::where('id', $Cid)->first();
        $wallet = spotWallet::where('user_id', $Uid)->where('crypto_currency_id',$Cid)->first();
        if ($request->amount > $wallet->available_balance) {
            $notify[] = ['error', 'Insufficient Balance'];
            return back()->withNotify($notify);
        }
        $wallet->balance = $wallet->balance - $request->amount;
        $wallet->available_balance = $wallet->available_balance - $request->amount;

        $admin=Admin::find(1);
        $transaction = new Transaction();
        $transaction->user_id = $Uid;
        $transaction->amount = $request->amount;
        $transaction->crypto_currency_id = $Cid;
        $transaction->trx_type = 'transfer';
        $transaction->post_balance = $wallet->balance;
        $transaction->trx = "spot_to_p2p";

        $transaction_charges=TransactionCharge::where('id','10')->first();
        $amount_usd=$request->amount*$cryptoCurr->rate;
        $charges=$amount_usd*$transaction_charges->percent_charge/100;
        $p2p_wallet = Wallet::where('user_id', $Uid)->where('crypto_currency_id', $Cid)->first();

        if ($p2p_wallet==null) {
            $p2p_wallet = new Wallet();
            $p2p_wallet->user_id = $Uid;
            $p2p_wallet->crypto_currency_id = $Cid;
            $p2p_wallet->balance = 0;
            $p2p_wallet->save();
        }

        $p2p_wallet->balance = $p2p_wallet->balance + $amount_usd - $charges;

        $admin->balance += $charges;
        $admin->save();
        $p2p_wallet->save();
        $wallet->save();
        $transaction->save();
        
        $notify[] = ['success', 'Transfer Successful'];
        return redirect("user/wallets")->withNotify($notify);
    }

    public function transfer_p2p(Request $request,string $Wid) {
        $pageTitle = 'Transfer - P2P to spot';
        $wallets = Wallet::where('id', $Wid)->with('crypto')->first();
        $transaction_charges=TransactionCharge::where('id','11')->first()->percent_charge;
        return view($this->activeTemplate . 'user.transfer_p2p', compact('pageTitle', 'wallets', 'Wid','transaction_charges'));
    }

    public function transfer_p2p_post(Request $request) {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'wallets_id' =>'required|numeric',
        ]);

        $wallet = Wallet::where('id',$request->wallets_id)->first();
        $Cid = $wallet->crypto_currency_id;
        $cryptoCurr = CryptoCurrency::where('id', $Cid)->first();

        if ($request->amount > $wallet->balance/$cryptoCurr->rate) {
            $notify[] = ['error', 'Insufficient Balance'];
            return back()->withNotify($notify);
        }

        $admin=Admin::find(1);

        $transaction = new Transaction();
        $transaction->user_id = auth()->id();
        $transaction->amount = $request->amount;
        $transaction->crypto_currency_id = $Cid;
        $transaction->trx_type = 'transfer';
        $transaction->post_balance = $wallet->balance/$cryptoCurr->rate;
        $transaction->trx = "p2p_to_spot";

        $transaction_charges=TransactionCharge::where('id','11')->first();
        $amount_curr=$request->amount;
        $charges=$amount_curr*$transaction_charges->percent_charge/100;

        $newbalance=$wallet->balance - $request->amount*$cryptoCurr->rate;
        if ($newbalance<=1.1) {
             $wallet->balance=0;
             $charges += $newbalance;
        }
        else{
            $wallet->balance=$newbalance;
        }

        $p2p_wallet = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $Cid)->first();

        if ($p2p_wallet==null) {
            $p2p_wallet = new spotWallet();
            $p2p_wallet->user_id = auth()->id();
            $p2p_wallet->crypto_currency_id = $Cid;
            $p2p_wallet->balance = 0;
            $p2p_wallet->available_balance = 0;
            $p2p_wallet->save();
        }



        $p2p_wallet->balance = $p2p_wallet->balance + $amount_curr - $charges;
        $p2p_wallet->available_balance = $p2p_wallet->available_balance + $amount_curr - $charges;
        $admin->balance += $charges*$cryptoCurr->rate;
        $admin->save();
        $p2p_wallet->save();
        $wallet->save();
        $transaction->save();

        $notify[] = ['success', 'Transfer Successful'];
        return redirect('user/spot-wallet')->withNotify($notify);
    }

    public function singleWallet($id, $code)
    {
        $pageTitle = $code . ' Wallets';
        $crypto = CryptoCurrency::findOrFail($id);

        $wallets = Wallet::where('user_id', auth()->user()->id)->with('crypto')->latest()->get();

        $cryptoWallets = CryptoWallet::where('user_id', auth()->user()->id)->where('crypto_currency_id', $crypto->id)->with('crypto')->latest()->take(1)->get();//->paginate(getPaginate());

        $emptyMessage = 'No wallet found';

        return view($this->activeTemplate . 'user.wallet', compact('pageTitle', 'wallets', 'cryptoWallets', 'crypto', 'emptyMessage'));
    }

    public function walletGenerate($code)
    {
        $crypto = CryptoCurrency::active()->where('code', $code)->first();

        if (!$crypto) {
            $notify[] = ['error', 'Generating new address with this crypto currency is currently disabled'];
            return back()->withNotify($notify);
        }

        $coinPayAcc = gs();
        $cps = new CoinPaymentHosted();
        $cps->Setup($coinPayAcc->private_key, $coinPayAcc->public_key);
        $callbackUrl = route('ipn.crypto');
        $result = $cps->GetCallbackAddress($crypto->code, $callbackUrl);
        if ($result['error'] == 'ok') {
            $newCryptoWallet = new CryptoWallet();
            $newCryptoWallet->user_id = auth()->user()->id;
            $newCryptoWallet->crypto_currency_id = $crypto->id;
            $newCryptoWallet->wallet_address = $result['result']['address'];
            $newCryptoWallet->save();
            $notify[] = ['success', 'New wallet address generated successfully.'];
        } else {
            $notify[] = ['error', 'API Error : ' . $result['error']];
        }

        return back()->withNotify($notify);
    }

    public function cryptoIpn(Request $request)
    {
        if ($request->status >= 100 || $request->status == 2) {

            $userCryptoWallet = CryptoWallet::where('wallet_address', $request->address)->first();
            $user = $userCryptoWallet->user;
            $general = gs();

            if ($general->merchant_id == $request->merchant) {

                $exist =  Deposit::where('cp_trx', $request->txn_id)->count();
                if ($exist == 0) {

                    $crypto = CryptoCurrency::find($userCryptoWallet->crypto_currency_id);
                    $sentAmount = $request->amount;

                    $charge                 = $crypto->deposit_charge_fixed + ($sentAmount * $crypto->deposit_charge_percent / 100);
                    $finalAmount            = $sentAmount - $charge;

                    if ($finalAmount > 0) {
                        $data                   = new Deposit();
                        $data->user_id          = $user->id;
                        $data->crypto_currency_id        = $crypto->id;
                        $data->wallet_address   = $request->address;
                        $data->amount           = $sentAmount;
                        $data->charge           = $charge;
                        $data->final_amo        = $finalAmount;
                        $data->trx              = getTrx();
                        $data->status           = 1;
                        $data->cp_trx           = $request->txn_id;
                        $data->save();

                        $userWallet = Wallet::where('user_id', $userCryptoWallet->user_id)->where('crypto_currency_id', $userCryptoWallet->crypto_currency_id)->first();
                        $userWallet->balance +=  $finalAmount;
                        $userWallet->save();

                        $transaction = new Transaction();
                        $transaction->user_id = $data->user_id;
                        $transaction->crypto_currency_id = $crypto->id;
                        $transaction->amount = $data->amount;
                        $transaction->post_balance = getAmount($userWallet->balance, 8);
                        $transaction->charge = getAmount($data->charge, 8);
                        $transaction->trx_type = '+';
                        $transaction->details = 'Deposit Via ' . $data->crypto->code;
                        $transaction->remark = 'deposit';
                        $transaction->trx = $data->trx;
                        $transaction->save();


                        $adminNotification = new AdminNotification();
                        $adminNotification->user_id = $user->id;
                        $adminNotification->title = 'Deposit successful for ' . $data->crypto->code;
                        $adminNotification->click_url = urlPath('admin.deposit.successful');
                        $adminNotification->save();

                        notify($user, 'DEPOSIT_COMPLETE', [
                            'amount' => showAmount($data->amount, 8),
                            'charge' => showAmount($data->charge, 8),
                            'currency' => $data->crypto->code,
                            'payable' => showAmount($data->final_amo, 8),
                            'trx' => $data->trx,
                            'post_balance' => showAmount($userWallet->balance, 8)
                        ]);

                        if ($general->deposit_commission) {
                            levelCommission($user, $data->amount, $crypto->id, $data->trx, 'deposit');
                        }
                    }
                }
            }
        }
    }
}
