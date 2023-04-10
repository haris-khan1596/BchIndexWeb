<?php

namespace App\Http\Controllers;

use App\Models\spotWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class P2pSpotTransfer extends Controller
{
     function index(){
         return view('templates.basic.user.spot-wallet');
     }

     function p2pTospot(){

     }

     public function spotWallet()
     {
         $pageTitle = 'Spot Wallet';
         $wallets = spotWallet::where('user_id', auth()->id())->with('crypto')->get();
        //  $data = DB::table('spot_wallets')->leftJoin('crypto_currencies','spot_wallets.crypto_currency_id','=','crypto_currencies.id')->get();
            $data = DB::table('spot_wallets')->leftJoin('crypto_currencies','spot_wallets.crypto_currency_id','=','crypto_currencies.id')->where('spot_wallets.user_id', auth()->id())->get();

         return view($this->activeTemplate . 'user.spot-wallet', compact('pageTitle','wallets','data'));
     }
}
?>
