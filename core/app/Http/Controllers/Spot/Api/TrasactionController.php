<?php

namespace App\Http\Controllers\Spot\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CryptoCurrency;
use App\Models\spotWallet;
use App\Models\Transaction;
use App\Models\TransactionCharge;
use App\Models\Wallet;
use App\Models\Admin;

class TrasactionController extends Controller
{
    
    public function transfer_p2p(Request $request) {
        // $pageTitle = 'Transfer - P2P to spot';
        // $wallets = Wallet::where('user_id', auth()->id())->with('crypto')->get();
        $wallets = Wallet::where('user_id', auth()->id())->with('crypto')->get();
        $wnames=array();
        $wids=array();
        foreach ($wallets as $wallet) {
            if ($wallet->balance!=0) {
                array_push($wnames,$wallet->crypto->code);
                $wids[$wallet->crypto->code]=['id'=>$wallet->id,'balance'=>$wallet->balance/$wallet->crypto->rate];
            }
            
        }
        $transaction_charges=TransactionCharge::where('id','11')->first()->percent_charge;
        // return view($this->activeTemplate . 'user.transfer_p2p', compact('wallets', 'Wid','transaction_charges'));
        return response()->json(['wallets' => $wnames,'walletDetails'=>$wids, 'transaction_charges' => $transaction_charges]);
    }

    public function transfer_spot(Request $request) {
        // $pageTitle = 'Transfer - Spot to P2P';
        $wallets = spotWallet::where('user_id',1 )->with('crypto')->get();
        $transaction_charges=TransactionCharge::where('id','10')->first()->percent_charge;
        $wnames=array();
        $wids=array();
        foreach ($wallets as $wallet) {
            if ($wallet->balance!=0) {
                array_push($wnames,$wallet->crypto->code);
                $wids[$wallet->crypto->code]=['id'=>$wallet->id,'balance'=>$wallet->available_balance
            ];
        }
    }
        // return view($this->activeTemplate . 'user.transfer_spot', compact('wallets', 'Wid','transaction_charges'));
        return response()->json(['wallets' => $wnames,'walletDetails'=>$wids, 'transaction_charges' => $transaction_charges]);
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
            return response()->json([
                'remark' => 'Insufficient Balance',
                'status' => 'failed',
                'message' => 'Insufficient Balance',
            ]);
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

        return response()->json([
            'remark' => 'Success',
            'status' => 'Success',
            'message' => 'Fund Transferred Successfully',
        ]);
    }

public function transfer_spot_post(Request $request) {
    $request->validate([
            'amount' => 'required|numeric|min:0',
            'wallets_id' =>'required|numeric',
        ]);

        $wallet = spotWallet::where('id',$request->wallets_id)->first();
        $Cid = $wallet->crypto_currency_id;
        $cryptoCurr = CryptoCurrency::where('id', $Cid)->first();

        if ($request->amount > $wallet->available_balance) {
            
            return response()->json([
                'remark' => 'Insufficient Balance',
                'status' => 'failed',
                'message' => 'Insufficent Balance',
            ]);
        }
        $wallet->balance -= $request->amount;
        $wallet->available_balance -= $request->amount;
        $admin=Admin::find(1);

        $transaction = new Transaction();
        $transaction->user_id = auth()->id();
        $transaction->amount = $request->amount;
        $transaction->crypto_currency_id = $Cid;
        $transaction->trx_type = 'transfer';
        $transaction->post_balance = $wallet->balance;
        $transaction->trx = "spot_to_p2p";

        $transaction_charges=TransactionCharge::where('id','10')->first();
        $amount_usd=$request->amount*$cryptoCurr->rate;
        $charges=$amount_usd*$transaction_charges->percent_charge/100;
        $p2p_wallet = Wallet::where('user_id', auth()->id())->where('crypto_currency_id', $Cid)->first();
        
        if ($p2p_wallet==null) {
            $p2p_wallet = new Wallet();
            $p2p_wallet->user_id = auth()->id();
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

        return response()->json([
            'remark' => 'Success',
            'status' => 'Success',
            'message' => 'Fund Transfered Successfully',
        ]);
}
}