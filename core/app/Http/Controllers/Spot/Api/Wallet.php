<?php

namespace App\Http\Controllers\Spot\Api;
use App\Models\spotWallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Wallet extends Controller
{
    public function index(Request $request, $id){
        $wallets = spotWallet::where('user_id', $id)->orderby('balance', 'asc')->with('crypto')->get();
        //$data = DB::table('spot_wallets')->leftJoin('crypto_currencies','spot_wallets.crypto_currency_id','=','crypto_currencies.id')->where('spot_wallets.user_id', auth()->id())->get();

        $balance = 0;
        foreach($wallets as $d)
        {
            $balance += $d->balance * $d->crypto->rate;
        }

         return response()->json([
            'wallets'=>$wallets,
            'balance'=>$balance,
         ]);
    }
}
