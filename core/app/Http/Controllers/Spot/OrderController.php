<?php

namespace App\Http\Controllers\Spot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SpotLimit;
use App\Models\SpotMarket;
use App\Models\SpotStopLimit;
use App\Models\SpotChart;
use App\Models\spotWallet;
use App\Models\CryptoCurrency;
use App\Models\CoinPair;
use App\Models\TransactionCharge;
use App\Models\Admin;
use App\Http\Controllers\Spot\ChartController;

class OrderController extends Controller
{
    public function SpotLimitOrder(Request $request)
    {
        $this->validate($request, [
            'pair' => 'required | numeric | exists:coin_pairs,id',
            'action' => 'required | in:buy,sell',
            'price' => 'required | numeric | min:0',
            'amount' => 'required | numeric | min:0',
        ]);

        // echo "<pre>";
        // print_r($request->all());
        // die;

        if($request->amount<=0){
            $notify[] = ['error', 'Amount cannot be 0'];
            return back()->withNotify($notify);
        }
        if($request->price<=0){
            $notify[] = ['error', 'Price cannot be 0'];
            return back()->withNotify($notify);
        }
        $bot_charges = TransactionCharge::where('id',12)->first()->percent_charge/100;

        // Check if user has enough balance to buy and if limit, Market or Stop Limit orders are 'open' and their combine amount is less than the balance
        $coinPair = CoinPair::find($request->pair);
        $quoteCoinId = $coinPair->CryptoCurrency_id1;
        $baseCoinId = $coinPair->crypto_currency_id;

        $wallet = null;

        if($request->action == 'buy'){

            $spotMarketOrders = SpotMarket::where('user_id', auth()->id())->where('coin_pair_id', $request->pair)->where('action', 'buy')->where('status', 'open')->get();


                    $closePrice = SpotChart::where('coin_pair_id', $request->pair)->where('close','!=',null)->orderBy('timeframe', 'desc')->first();
                    if ($closePrice == null) {
                        $closePrice = CryptoCurrency::where('id', $baseCoinId)->first()->rate / CryptoCurrency::where('id', $quoteCoinId)->first()->rate;
                    }
                    else{
                        $closePrice = $closePrice->close;
                    }

                    $openOrders = 0;
                    foreach($spotMarketOrders as $spotMarketOrder){
                        $openOrders += $spotMarketOrder->amount * $closePrice*(1+$bot_charges);
                    }

            // whole block is dedicated to find if user has enough balance to buy even if he has placed some open orders

            $wallet = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $quoteCoinId)->first();

            if ($wallet!=null) {
                $balance = $wallet->available_balance;

                $balance -= $openOrders;

                if ($balance < $request->amount * $request->price) {
                    $notify[] = ['error', 'Insufficient balance'];
                    return back()->withNotify($notify);
                }
                else{
                    $wallet->available_balance -= $request->amount * $request->price;
                }
            }
            else {
                $notify[] = ['error', 'Wallet of '.CryptoCurrency::where('id',$quoteCoinId)->first()->code .' not found'];
                return back()->withNotify($notify);
            }
        }
        else{
            $wallet = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $baseCoinId)->first();
            if($wallet!=null){
                $balance = $wallet->available_balance;
                if($balance < $request->amount){
                    $notify[] = ['error', 'Insufficient balance'];
                    return back()->withNotify($notify);
                }
                else{
                    $wallet->available_balance -= $request->amount;
                }
            }
            else{
                $notify[] = ['error', 'Wallet of '.CryptoCurrency::where('id',$baseCoinId)->first()->code .' not found'];
                return back()->withNotify($notify);
            }
        }

        $spotLimit = new SpotLimit();
        $spotLimit->user_id = auth()->id();
        $spotLimit->coin_pair_id = $request->pair;
        $spotLimit->action = $request->action;
        $spotLimit->price = $request->price;
        $spotLimit->amount = $request->amount;
        $spotLimit->status = 'open';
        $spotLimit->save();
        $wallet->save();

        OrderController::CronOrders($request->pair);
        $notify[] = ['success', 'Order placed successfully'];
        return back()->withNotify($notify);

    }

    public function SpotStopLimitOrder(Request $request)
    {
        $this->validate($request, [
            'pair' => 'required | numeric | exists:coin_pairs,id',
            'action' => 'required | in:buy,sell',
            'price' => 'required | numeric | min:0',
            'amount' => 'required | numeric | min:0',
            'stop_price' => 'required | numeric | min:0',
        ]);

        if($request->amount<=0){
            $notify[] = ['error', 'Amount cannot be 0'];
            return back()->withNotify($notify);
        }
        if($request->price<=0){
            $notify[] = ['error', 'Price cannot be 0'];
            return back()->withNotify($notify);
        }
        if($request->stop_price<=0){
            $notify[] = ['error', 'Stop Price cannot be 0'];
            return back()->withNotify($notify);
        }

                // Check if user has enough balance to buy and if limit, Market or Stop Limit orders are 'open' and their combine amount is less than the balance
                $coinPair = CoinPair::find($request->pair);
                $quoteCoinId = $coinPair->CryptoCurrency_id1;
                $baseCoinId = $coinPair->crypto_currency_id;

                $wallet = null;
                $bot_charges = TransactionCharge::where('id',12)->first()->percent_charge/100;

        if($request->action == 'buy'){
            $spotMarketOrders = SpotMarket::where('user_id', auth()->id())->where('coin_pair_id', $request->pair)->where('action', 'buy')->where('status', 'open')->get();


                    $closePrice = SpotChart::where('coin_pair_id', $request->pair)->where('close','!=',null)->orderBy('timeframe', 'desc')->first();
                    if ($closePrice == null) {
                        $closePrice = CryptoCurrency::where('id', $baseCoinId)->first()->rate / CryptoCurrency::where('id', $quoteCoinId)->first()->rate;
                    }
                    else{
                        $closePrice = $closePrice->close;
                    }

                    $openOrders = 0;
                    foreach($spotMarketOrders as $spotMarketOrder){
                        $openOrders += $spotMarketOrder->amount * $closePrice*(1+$bot_charges);
                    }

            // whole block is dedicated to find if user has enough balance to buy even if he has placed some open orders

            $wallet = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $quoteCoinId)->first();
            if ($wallet!=null) {
                $balance = $wallet->available_balance;

                $balance -= $openOrders;

                if ($balance < $request->amount * $request->price) {
                    $notify[] = ['error', 'Insufficient balance'];
                    return back()->withNotify($notify);
                }
                else{
                    $wallet->available_balance -= $request->amount * $request->price;
                }
            }
            else {
                $notify[] = ['error', 'Wallet of '.CryptoCurrency::where('id',$quoteCoinId)->first()->code .' not found'];
                return back()->withNotify($notify);
            }
        }
        else{
            $wallet = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $baseCoinId)->first();
            if($wallet!=null){
                $balance = $wallet->available_balance;
                if($balance < $request->amount){
                    $notify[] = ['error', 'Insufficient balance'];
                    return back()->withNotify($notify);
                }
                else{
                    $wallet->available_balance -= $request->amount;
                }
            }
            else{
                $notify[] = ['error', 'Wallet of '.CryptoCurrency::where('id',$baseCoinId)->first()->code .' not found'];
                return back()->withNotify($notify);
            }
        }
                // if($request->action == 'buy'){
                //     $wallet = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $quoteCoinId)->first();
                //     if ($wallet!=null) {
                //         $balance = $wallet->balance;
                //     }
                //     else {
                //         $notify[] = ['error', 'Wallet of '.CryptoCurrency::where('id',$quoteCoinId)->first()->code .' not found'];
                //         return back()->withNotify($notify);
                //     }
                //     $openOrders = 0;
                //     $spotLimitOrders = SpotLimit::where('user_id', auth()->id())->where('coin_pair_id', $request->pair)->where('action', 'buy')->where('status', 'open')->get();

                //     foreach($spotLimitOrders as $spotLimitOrder){
                //         $openOrders += $spotLimitOrder->amount * $spotLimitOrder->price;
                //     }

                //     $spotMarketOrders = SpotMarket::where('user_id', auth()->id())->where('coin_pair_id', $request->pair)->where('action', 'buy')->where('status', 'open')->get();

                //     $closePrice = SpotChart::where('coin_pair_id', $request->pair)->where('close','!=',null)->orderBy('timeframe', 'desc')->first();
                //     if ($closePrice == null) {
                //         $closePrice = CryptoCurrency::where('id', $baseCoinId)->first()->rate / CryptoCurrency::where('id', $quoteCoinId)->first()->rate;
                //     }
                //     else{
                //         $closePrice = $closePrice->close;
                //     }

                //     foreach($spotMarketOrders as $spotMarketOrder){
                //         $openOrders += $spotMarketOrder->amount * $closePrice;
                //     }

                //     $spotStopLimitOrders = SpotStopLimit::where('user_id', auth()->id())->where('coin_pair_id', $request->pair)->where('action', 'buy')->where('status', 'open')->get();

                //     foreach($spotStopLimitOrders as $spotStopLimitOrder){
                //         $openOrders += $spotStopLimitOrder->amount * $spotStopLimitOrder->price;
                //     }

                //     if($balance < $openOrders + ($request->amount * $request->price)){
                //         $notify[] = ['error', 'Insufficient balance'];
                //         return back()->withNotify($notify);
                //     }
                // }
                // else{
                //     $wallet = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $baseCoinId)->first();
                //     if($wallet!=null){
                //         $balance = $wallet->balance;
                //     }
                //     else{
                //         $notify[] = ['error', 'Wallet of '.CryptoCurrency::where('id',$baseCoinId)->first()->code .' not found'];
                //         return back()->withNotify($notify);
                //     }
                //     $openOrders = SpotLimit::where('user_id', auth()->id())->where('coin_pair_id', $request->pair)->where('action', 'sell')->where('status', 'open')->sum('amount');

                //     $openOrders += SpotMarket::where('user_id', auth()->id())->where('coin_pair_id', $request->pair)->where('action', 'sell')->where('status', 'open')->sum('amount');

                //     $openOrders += SpotStopLimit::where('user_id', auth()->id())->where('coin_pair_id', $request->pair)->where('action', 'sell')->where('status', 'open')->sum('amount');

                //     if($balance < $openOrders + $request->amount){
                //         $notify[] = ['error', 'Insufficient balance'];
                //         return back()->withNotify($notify);
                //     }
                // }

        $spotStopLimit = new SpotStopLimit();
        $spotStopLimit->user_id = auth()->id();
        $spotStopLimit->coin_pair_id = $request->pair;
        $spotStopLimit->action = $request->action;
        $spotStopLimit->price = $request->price;
        $spotStopLimit->amount = $request->amount;
        $spotStopLimit->status = 'open';
        $spotStopLimit->stop_price = $request->stop_price;
        $spotStopLimit->save();
        $wallet->save();

        $this->CronOrders($request->pair);
        $notify[] = ['success', 'Order placed successfully'];
        return back()->withNotify($notify);
    }

    public function SpotMarketOrder(Request $request)
    {
        $this->validate($request, [
            'pair' => 'required | numeric | exists:coin_pairs,id',
            'action' => 'required | in:buy,sell',
            'amount' => 'required | numeric | min:0',
        ]);



        if($request->amount==0){
            $notify[] = ['error', 'Amount cannot be 0'];
            return back()->withNotify($notify);
        }
            $bot_charges = TransactionCharge::where('id',12)->first()->percent_charge/100;
            $admin = Admin::first();

                // Check if user has enough balance to buy and if limit, Market or Stop Limit orders are 'open' and their combine amount is less than the balance
                $coinPair = CoinPair::find($request->pair);
                $quoteCoinId = $coinPair->CryptoCurrency_id1;
                $baseCoinId = $coinPair->crypto_currency_id;

                // if($request->action == 'buy'){
                //     $wallet = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $quoteCoinId)->first();
                //     if($wallet!=null){
                //         $balance = $wallet->balance;
                //     }
                //     else{
                //         $notify[] = ['error', 'Wallet of '.CryptoCurrency::where('id',$quoteCoinId)->first()->code .' not found'];
                //         return back()->withNotify($notify);
                //     }
                //     $openOrders = 0;
                //     $spotLimitOrders = SpotLimit::where('user_id', auth()->id())->where('coin_pair_id', $request->pair)->where('action', 'buy')->where('status', 'open')->get();

                //     foreach($spotLimitOrders as $spotLimitOrder){
                //         $openOrders += $spotLimitOrder->amount * $spotLimitOrder->price;
                //     }

                //     $spotMarketOrders = SpotMarket::where('user_id', auth()->id())->where('coin_pair_id', $request->pair)->where('action', 'buy')->where('status', 'open')->get();


                //     $closePrice = SpotChart::where('coin_pair_id', $request->pair)->where('close','!=',null)->orderBy('timeframe', 'desc')->first();
                //     if ($closePrice == null) {
                //         $closePrice = CryptoCurrency::where('id', $baseCoinId)->first()->rate / CryptoCurrency::where('id', $quoteCoinId)->first()->rate;
                //     }
                //     else{
                //         $closePrice = $closePrice->close;
                //     }


                //     foreach($spotMarketOrders as $spotMarketOrder){
                //         $openOrders += $spotMarketOrder->amount * $closePrice;
                //     }

                //     $spotStopLimitOrders = SpotStopLimit::where('user_id', auth()->id())->where('coin_pair_id', $request->pair)->where('action', 'buy')->where('status', 'open')->get();

                //     foreach($spotStopLimitOrders as $spotStopLimitOrder){
                //         $openOrders += $spotStopLimitOrder->amount * $spotStopLimitOrder->price;
                //     }

                //     if($balance < $openOrders + ($request->amount * $closePrice)){
                //         $notify[] = ['error', 'Insufficient balance'];
                //         return back()->withNotify($notify);
                //     }
                // }
                // else{
                //     $wallet = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $baseCoinId)->first();

                //     if($wallet!=null){
                //         $balance = $wallet->balance;
                //     }
                //     else{
                //         $notify[] = ['error', 'Wallet of '.CryptoCurrency::where('id',$baseCoinId)->first()->code .' not found'];
                //         return back()->withNotify($notify);
                //     }
                //     $openOrders = SpotLimit::where('user_id', auth()->id())->where('coin_pair_id', $request->pair)->where('action', 'sell')->where('status', 'open')->sum('amount');

                //     $openOrders += SpotMarket::where('user_id', auth()->id())->where('coin_pair_id', $request->pair)->where('action', 'sell')->where('status', 'open')->sum('amount');

                //     $openOrders += SpotStopLimit::where('user_id', auth()->id())->where('coin_pair_id', $request->pair)->where('action', 'sell')->where('status', 'open')->sum('amount');

                //     if($balance < $openOrders + $request->amount){
                //         $notify[] = ['error', 'Insufficient balance'];
                //         return back()->withNotify($notify);
                //     }
                // }
                $wallet = null;

                if($request->action == 'buy'){
                    $spotMarketOrders = SpotMarket::where('user_id', auth()->id())->where('coin_pair_id', $request->pair)->where('action', 'buy')->where('status', 'open')->get();


                            $closePrice = SpotChart::where('coin_pair_id', $request->pair)->where('close','!=',null)->orderBy('timeframe', 'desc')->first();
                            if ($closePrice == null) {
                                $closePrice = CryptoCurrency::where('id', $baseCoinId)->first()->rate / CryptoCurrency::where('id', $quoteCoinId)->first()->rate;
                            }
                            else{
                                $closePrice = $closePrice->close;
                            }

                            $openOrders = 0;
                            foreach($spotMarketOrders as $spotMarketOrder){
                                $openOrders += $spotMarketOrder->amount * $closePrice*(1+$bot_charges);
                            }

                    // whole block is dedicated to find if user has enough balance to buy even if he has placed some open orders

                    $wallet = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $quoteCoinId)->first();
                    if ($wallet!=null) {
                        $balance = $wallet->available_balance;

                        $balance -= $openOrders;

                        if ($balance < $request->amount * $closePrice) {
                            $notify[] = ['error', 'Insufficient balance'];
                            return back()->withNotify($notify);
                        }

                    }
                    else {
                        $notify[] = ['error', 'Wallet of '.CryptoCurrency::where('id',$quoteCoinId)->first()->code .' not found'];
                        return back()->withNotify($notify);
                    }
                }
                else{
                    // sell market order

                    $wallet = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $baseCoinId)->first();
                    if($wallet!=null){
                        $balance = $wallet->available_balance;
                        if($balance < $request->amount){
                            $notify[] = ['error', 'Insufficient balance'];
                            return back()->withNotify($notify);
                        }
                        else{
                            $wallet->available_balance -= $request->amount;
                        }
                    }
                    else{
                        $notify[] = ['error', 'Wallet of '.CryptoCurrency::where('id',$baseCoinId)->first()->code .' not found'];
                        return back()->withNotify($notify);
                    }
                }



        $spotMarket = new SpotMarket();
        $spotMarket->user_id = auth()->id();
        $spotMarket->coin_pair_id = $request->pair;
        $spotMarket->action = $request->action;
        $spotMarket->amount = $request->amount;
        $spotMarket->status = 'open';
        $spotMarket->save();
        $wallet->save();
        $coinPair = CoinPair::where('id', $request->pair)->first();
        $coinId1 = $coinPair->crypto_currency_id;
        $coinId2 = $coinPair->CryptoCurrency_id1;

        // create user's wallet if not exist
        $Mwallet1 = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $coinId1)->first();
        if ($Mwallet1 == null)
        {
            $Mwallet1 = new spotWallet();
            $Mwallet1->user_id = auth()->id();
            $Mwallet1->crypto_currency_id = $coinId1;
            $Mwallet1->balance = 0;
            $Mwallet1->available_balance = 0;
            $Mwallet1->save();
        }

        $Mwallet2 = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $coinId2)->first();
        if ($Mwallet2 == null)
        {
            $Mwallet2 = new spotWallet();
            $Mwallet2->user_id = auth()->id();
            $Mwallet2->crypto_currency_id = $coinId2;
            $Mwallet2->balance = 0;
            $Mwallet2->available_balance = 0;
            $Mwallet2->save();
        }

        if($request->action == 'buy')
        {
            $spotLimits = SpotLimit::where('coin_pair_id', $request->pair)->where('action', 'sell')->where('status', 'open')->orderBy('price', 'asc')->get();

            foreach ($spotLimits as $spotLimit)
            {
                $Swallet1 = spotWallet::where('user_id', $spotLimit->user_id)->where('crypto_currency_id', $coinId1)->first();
                if ($Swallet1 == null)
                {
                    $Swallet1 = new spotWallet();
                    $Swallet1->user_id = $spotLimit->user_id;
                    $Swallet1->crypto_currency_id = $coinId1;
                    $Swallet1->balance = 0;
                    $Swallet1->available_balance = 0;
                    $Swallet1->save();
                }

                $Swallet2 = spotWallet::where('user_id', $spotLimit->user_id)->where('crypto_currency_id', $coinId2)->first();
                if ($Swallet2 == null)
                {
                    $Swallet2 = new spotWallet();
                    $Swallet2->user_id = $spotLimit->user_id;
                    $Swallet2->crypto_currency_id = $coinId2;
                    $Swallet2->balance = 0;
                    $Swallet2->available_balance = 0;
                    $Swallet2->save();
                }

                //Swallet1 is seller's base coin wallet
                //Swallet2 is seller's quote coin wallet
                //Mwallet1 is buyer's base coin wallet
                //Mwallet2 is buyer's quote coin wallet

                // if

                    if($spotMarket->amount >= $spotLimit->amount)
                    {
                        $spotMarket->amount -= $spotLimit->amount;
                        $spotMarket->filled += $spotLimit->amount;
                        $spotLimit->filled += $spotLimit->amount;

                        $spotLimit->status = 'closed';

                        $Swallet1->balance -= $spotLimit->amount;
                        $Swallet1->save();

                        $Mwallet1->balance += $spotLimit->amount;
                        $Mwallet1->available_balance += $spotLimit->amount;
                        $Mwallet1->save();

                        $Swallet2->balance += $spotLimit->amount * $spotLimit->price;
                        $Swallet2->available_balance += $spotLimit->amount * $spotLimit->price;
                        $Swallet2->save();

                        $Mwallet2->balance -= $spotLimit->amount * ($spotLimit->price  *(1+$bot_charges));
                        $Mwallet2->available_balance -= $spotLimit->amount * ($spotLimit->price  *(1+$bot_charges));
                        $admin->balance+= ($spotLimit->amount * $spotLimit->price * $bot_charges)/CryptoCurrency::where('id',$Mwallet2->crypto_currency_id)->first()->rate;
                        $Mwallet2->save();

                        $spotLimit->amount = 0;

                        $spotMarket->price = $spotLimit->price;

                        ChartController::UpdateCandle($request->pair, $spotLimit->price);
                        $this->CronStopLimitOrders($request->pair, $spotLimit->price);

                        $spotLimit->save();

                        if ($spotMarket->amount == 0)
                        {
                            $spotMarket->status = 'closed';
                            $spotMarket->save();
                            break;
                        }

                    }
                    else
                    {
                        $spotLimit->amount -= $spotMarket->amount;
                        $spotMarket->filled += $spotMarket->amount;
                        $spotLimit->filled += $spotMarket->amount;

                        $Swallet1->balance -= $spotMarket->amount;
                        $Swallet1->save();

                        $Mwallet1->balance += $spotMarket->amount;
                        $Mwallet1->available_balance += $spotMarket->amount;
                        $Mwallet1->save();

                        $Swallet2->balance += $spotMarket->amount * $spotLimit->price;
                        $Swallet2->available_balance += $spotMarket->amount * $spotLimit->price;
                        $Swallet2->save();

                        $Mwallet2->balance -= $spotMarket->amount * $spotLimit->price*(1+$bot_charges);
                        $Mwallet2->available_balance -= $spotMarket->amount * $spotLimit->price*(1+$bot_charges);
                        $admin->balance+= ($spotLimit->amount * $spotLimit->price * $bot_charges)/CryptoCurrency::where('id',$Mwallet2->crypto_currency_id)->first()->rate;
                        $Mwallet2->save();

                        $spotMarket->price = $spotLimit->price;

                        ChartController::UpdateCandle($request->pair, $spotLimit->price);
                        $this->CronStopLimitOrders($request->pair, $spotLimit->price);

                        $spotLimit->save();
                        $admin->save();
                        $spotMarket->amount = 0;
                        $spotMarket->status = 'closed';
                        $spotMarket->save();
                        $notify[] = ['success', 'Order completed successfully'];
                        return back()->withNotify($notify);
                    }
            }
        }
        else
        {
            $spotLimits = SpotLimit::where('coin_pair_id', $request->pair)->where('action', 'buy')->orderBy('price', 'desc')->get();

            foreach($spotLimits as $spotLimit)
            {
                $Swallet1 = spotWallet::where('user_id', $spotLimit->user_id)->where('crypto_currency_id', $coinId1)->first();
                if ($Swallet1 == null)
                {
                    $Swallet1 = new spotWallet();
                    $Swallet1->user_id = $spotLimit->user_id;
                    $Swallet1->crypto_currency_id = $coinId1;
                    $Swallet1->balance = 0;
                    $Swallet1->available_balance = 0;
                    $Swallet1->save();
                }


                $Swallet2 = spotWallet::where('user_id', $spotLimit->user_id)->where('crypto_currency_id', $coinId2)->first();
                if ($Swallet2 == null){
                    $Swallet2 = new spotWallet();
                    $Swallet2->user_id = $spotLimit->user_id;
                    $Swallet2->crypto_currency_id = $coinId2;
                    $Swallet2->balance = 0;
                    $Swallet2->available_balance = 0;
                    $Swallet2->save();
                }

                    if($spotMarket->amount >= $spotLimit->amount)
                    {
                        $spotMarket->amount -= $spotLimit->amount;
                        $spotMarket->filled += $spotLimit->amount;
                        $spotLimit->filled += $spotLimit->amount;
                        $spotLimit->status = 'closed';

                        $Swallet1->balance += $spotLimit->amount;
                        $Swallet1->available_balance += $spotLimit->amount;
                        $Swallet1->save();

                        $Mwallet1->balance -= $spotLimit->amount;
                        $Mwallet1->save();

                        $Swallet2->balance -= $spotLimit->amount * $spotLimit->price;
                        $Swallet2->save();

                        $Mwallet2->balance += $spotLimit->amount * $spotLimit->price * (1-$bot_charges);
                        $Mwallet2->available_balance += $spotLimit->amount * $spotLimit->price* (1-$bot_charges);
                        $admin->balance+= ($spotLimit->amount * $spotLimit->price * $bot_charges)/CryptoCurrency::where('id',$Mwallet2->crypto_currency_id)->first()->rate;
                        $Mwallet2->save();

                        $spotLimit->amount = 0;

                        $spotMarket->price = $spotLimit->price;

                        ChartController::UpdateCandle($request->pair, $spotLimit->price);
                        $this->CronStopLimitOrders($request->pair, $spotLimit->price);

                        $spotLimit->save();
                        if ($spotMarket->amount == 0)
                        {
                            $spotMarket->status = 'closed';
                            $spotMarket->save();
                            break;
                        }

                    }
                    else
                    {
                        $spotLimit->amount -= $spotMarket->amount;
                        $spotMarket->filled += $spotMarket->amount;
                        $spotLimit->filled += $spotMarket->amount;

                        $Swallet1->balance += $spotMarket->amount;
                        $Swallet1->available_balance += $spotMarket->amount;
                        $Swallet1->save();

                        $Mwallet1->balance -= $spotMarket->amount;
                        $Mwallet1->save();

                        $Swallet2->balance -= $spotMarket->amount * $spotLimit->price;
                        $Swallet2->save();

                        $Mwallet2->balance += $spotLimit->amount * $spotLimit->price * (1-$bot_charges);
                        $Mwallet2->available_balance += $spotLimit->amount * $spotLimit->price* (1-$bot_charges);
                        $admin->balance+= ($spotLimit->amount * $spotLimit->price * $bot_charges)/CryptoCurrency::where('id',$Mwallet2->crypto_currency_id)->first()->rate;
                        $Mwallet2->save();

                        $spotMarket->price = $spotLimit->price;

                        ChartController::UpdateCandle($request->pair, $spotLimit->price);
                        $this->CronStopLimitOrders($request->pair, $spotLimit->price);

                        $spotLimit->save();
                        $spotMarket->amount = 0;
                        $spotMarket->status = 'closed';
                        $spotMarket->save();
                        $admin->save();
                        $notify[] = ['success', 'Order completed successfully'];
                        return back()->withNotify($notify);
                    }
            }
        }

        $spotMarket->save();
        $admin->save();
        $notify[] = ['success', 'Order placed successfully'];
        return back()->withNotify($notify);
    }

    public static function CronOrders(string $pair)
    {
        $bot_charges = TransactionCharge::where('id',12)->first()->percent_charge/100;
        $admin = Admin::first();
        $coinPair = CoinPair::where('id', $pair)->first();
        $coinId1 = $coinPair->crypto_currency_id;
        $coinId2 = $coinPair->CryptoCurrency_id1;

        $spotMarkets = SpotMarket::where('status', 'open')->where('coin_pair_id', $pair)->get();

        foreach($spotMarkets as $spotMarket)
        {



        $Mwallet1 = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $coinId1)->first();
        if ($Mwallet1 == null)
        {
            $Mwallet1 = new spotWallet();
            $Mwallet1->user_id = auth()->id();
            $Mwallet1->crypto_currency_id = $coinId1;
            $Mwallet1->balance = 0;
            $Mwallet1->available_balance = 0;
            $Mwallet1->save();
        }

        $Mwallet2 = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $coinId2)->first();
        if ($Mwallet2 == null)
        {
            $Mwallet2 = new spotWallet();
            $Mwallet2->user_id = auth()->id();
            $Mwallet2->crypto_currency_id = $coinId2;
            $Mwallet2->balance = 0;
            $Mwallet2->available_balance = 0;
            $Mwallet2->save();
        }

        if($spotMarket->action == 'buy')
        {

            $spotLimits = SpotLimit::where('coin_pair_id', $pair)->where('action', 'sell')->where('status', 'open')->orderBy('price', 'asc')->get();

            $count = count($spotLimits);



            foreach ($spotLimits as $spotLimit)
            {

                $Swallet1 = spotWallet::where('user_id', $spotLimit->user_id)->where('crypto_currency_id', $coinId1)->first();
                if ($Swallet1 == null)
                {
                    $Swallet1 = new spotWallet();
                    $Swallet1->user_id = $spotLimit->user_id;
                    $Swallet1->crypto_currency_id = $coinId1;
                    $Swallet1->balance = 0;
                    $Swallet1->available_balance = 0;
                    $Swallet1->save();
                }

                $Swallet2 = spotWallet::where('user_id', $spotLimit->user_id)->where('crypto_currency_id', $coinId2)->first();
                if ($Swallet2 == null){
                    $Swallet2 = new spotWallet();
                    $Swallet2->user_id = $spotLimit->user_id;
                    $Swallet2->crypto_currency_id = $coinId2;
                    $Swallet2->balance = 0;
                    $Swallet2->available_balance = 0;
                    $Swallet2->save();
                }

                    if($spotMarket->amount >= $spotLimit->amount)
                    {

                        $spotMarket->amount -= $spotLimit->amount;
                        $spotMarket->filled += $spotLimit->amount;
                        $spotLimit->filled += $spotLimit->amount;
                        $spotLimit->status = 'closed';

                        $Swallet1->balance -= $spotLimit->amount;
                        $Swallet1->save();

                        $Mwallet1->balance += $spotLimit->amount;
                        $Mwallet1->available_balance += $spotLimit->amount;
                        $Mwallet1->save();

                        $Swallet2->balance += $spotLimit->amount * $spotLimit->price;
                        $Swallet2->available_balance += $spotLimit->amount * $spotLimit->price;
                        $Swallet2->save();

                        $Mwallet2->balance -= $spotLimit->amount * ($spotLimit->price  *(1+$bot_charges));
                        $Mwallet2->available_balance -= $spotLimit->amount * ($spotLimit->price  *(1+$bot_charges));
                        $admin->balance+= ($spotLimit->amount * $spotLimit->price * $bot_charges)/CryptoCurrency::where('id',$Mwallet2->crypto_currency_id)->first()->rate;

                        $Mwallet2->save();

                        $spotLimit->amount = 0;

                        $spotMarket->price = $spotLimit->price;

                        ChartController::UpdateCandle($pair, $spotLimit->price);
                        OrderController::CronStopLimitOrders($pair, $spotLimit->price);

                        $spotLimit->save();

                        if($spotMarket->amount == 0)
                        {
                            $spotMarket->status = 'closed';
                            $spotMarket->save();
                            break;
                        }

                    }
                    else
                    {

                        $spotLimit->amount -= $spotMarket->amount;
                        $spotMarket->filled += $spotMarket->amount;
                        $spotLimit->filled += $spotMarket->amount;

                        $Swallet1->balance -= $spotMarket->amount;
                        $Swallet1->save();

                        $Mwallet1->balance += $spotMarket->amount;
                        $Mwallet1->available_balance += $spotMarket->amount;
                        $Mwallet1->save();

                        $Swallet2->balance += $spotMarket->amount * $spotLimit->price;
                        $Swallet2->available_balance += $spotMarket->amount * $spotLimit->price;
                        $Swallet2->save();

                        $Mwallet2->balance -= $spotLimit->amount * ($spotLimit->price  *(1+$bot_charges));
                        $Mwallet2->available_balance -= $spotLimit->amount * ($spotLimit->price  *(1+$bot_charges));
                        $admin->balance+= ($spotLimit->amount * $spotLimit->price * $bot_charges)/CryptoCurrency::where('id',$Mwallet2->crypto_currency_id)->first()->rate;
                        $Mwallet2->save();

                        $spotMarket->price = $spotLimit->price;

                        ChartController::UpdateCandle($pair, $spotLimit->price);
                        OrderController::CronStopLimitOrders($pair, $spotLimit->price);

                        $spotLimit->save();
                        $spotMarket->amount = 0;
                        $spotMarket->status = 'closed';
                        $spotMarket->save();

                    }
            }
        }
        else
        {

            $spotLimits = SpotLimit::where('coin_pair_id',$pair)->where('action', 'buy')->orderBy('price', 'desc')->get();
            $count = count($spotLimits);

            foreach($spotLimits as $spotLimit)
            {

                $Swallet1 = spotWallet::where('user_id', $spotLimit->user_id)->where('crypto_currency_id', $coinId1)->first();
                if ($Swallet1 == null)
                {
                    $Swallet1 = new spotWallet();
                    $Swallet1->user_id = $spotLimit->user_id;
                    $Swallet1->crypto_currency_id = $coinId1;
                    $Swallet1->balance = 0;
                    $Swallet1->available_balance = 0;
                    $Swallet1->save();
                }


                $Swallet2 = spotWallet::where('user_id', $spotLimit->user_id)->where('crypto_currency_id', $coinId2)->first();
                if ($Swallet2 == null){
                    $Swallet2 = new spotWallet();
                    $Swallet2->user_id = $spotLimit->user_id;
                    $Swallet2->crypto_currency_id = $coinId2;
                    $Swallet2->balance = 0;
                    $Swallet2->available_balance = 0;
                    $Swallet2->save();
                }

                    if($spotMarket->amount >= $spotLimit->amount)
                    {

                        $spotMarket->amount -= $spotLimit->amount;
                        $spotMarket->filled += $spotLimit->amount;
                        $spotLimit->filled += $spotLimit->amount;
                        $spotLimit->status = 'closed';

                        $Swallet1->balance += $spotLimit->amount;
                        $Swallet1->available_balance += $spotLimit->amount;
                        $Swallet1->save();

                        $Mwallet1->balance -= $spotLimit->amount;
                        $Mwallet1->save();

                        $Swallet2->balance -= $spotLimit->amount * $spotLimit->price;
                        $Swallet2->save();

                        $Mwallet2->balance += $spotLimit->amount * ($spotLimit->price  *(1-$bot_charges));
                        $Mwallet2->available_balance += $spotLimit->amount * ($spotLimit->price  *(1-$bot_charges));
                        $admin->balance+= ($spotLimit->amount * $spotLimit->price * $bot_charges)/CryptoCurrency::where('id',$Mwallet2->crypto_currency_id)->first()->rate;
                        $Mwallet2->save();

                        $spotLimit->amount = 0;

                        $spotMarket->price = $spotLimit->price;

                        ChartController::UpdateCandle($pair, $spotLimit->price);
                        OrderController::CronStopLimitOrders($pair, $spotLimit->price);

                        $spotLimit->save();
                        if ($spotMarket->amount == 0)
                        {
                            $spotMarket->status = 'closed';
                            $spotMarket->save();
                            break;
                        }

                    }
                    else
                    {

                        $spotLimit->amount -= $spotMarket->amount;
                        $spotMarket->filled += $spotMarket->amount;
                        $spotLimit->filled += $spotMarket->amount;

                        $Swallet1->balance += $spotMarket->amount;
                        $Swallet1->available_balance += $spotMarket->amount;
                        $Swallet1->save();

                        $Mwallet1->balance -= $spotMarket->amount;
                        $Mwallet1->save();

                        $Swallet2->balance -= $spotMarket->amount * $spotLimit->price;
                        $Swallet2->save();

                        $Mwallet2->balance += $spotLimit->amount * ($spotLimit->price  *(1-$bot_charges));
                        $Mwallet2->available_balance += $spotLimit->amount * ($spotLimit->price  *(1-$bot_charges));
                        $admin->balance+= ($spotLimit->amount * $spotLimit->price * $bot_charges)/CryptoCurrency::where('id',$Mwallet2->crypto_currency_id)->first()->rate;
                        $Mwallet2->save();

                        $spotMarket->price = $spotLimit->price;

                        ChartController::UpdateCandle($pair, $spotLimit->price);
                        OrderController::CronStopLimitOrders($pair, $spotLimit->price);

                        $spotLimit->save();
                        $admin->save();
                        $spotMarket->amount = 0;
                        $spotMarket->status = 'closed';
                        $spotMarket->save();

                    }
            }
        }

        }


    }

    public static function CronStopLimitOrders(string $price, string $pair)
    {
        $spotStopLimits = SpotStopLimit::where('status', 'open')->where('coin_pair_id',$pair)->get();
        foreach($spotStopLimits as $spotStopLimit)
        {
            if($spotStopLimit->action == 'buy')
            {
                if($price <= $spotStopLimit->stop_price)
                {
                    $spotStopLimit->status = 'closed';
                    $spotStopLimit->save();

                    $spotLimit = new SpotLimit();
                    $spotLimit->user_id = $spotStopLimit->user_id;
                    $spotLimit->coin_pair_id = $spotStopLimit->coin_pair_id;
                    $spotLimit->amount = $spotStopLimit->amount;
                    $spotLimit->filled = 0;
                    $spotLimit->price = $spotStopLimit->price;
                    $spotLimit->action = $spotStopLimit->action;
                    $spotLimit->status = 'open';
                    $spotLimit->save();

                }
            }
            else
            {
                if($price >= $spotStopLimit->stop_price)
                {
                    $spotStopLimit->status = 'closed';
                    $spotStopLimit->save();

                    $spotLimit = new SpotLimit();
                    $spotLimit->user_id = $spotStopLimit->user_id;
                    $spotLimit->coin_pair_id = $spotStopLimit->coin_pair_id;
                    $spotLimit->amount = $spotStopLimit->amount;
                    $spotLimit->filled = 0;
                    $spotLimit->price = $spotStopLimit->price;
                    $spotLimit->action = $spotStopLimit->action;
                    $spotLimit->status = 'open';
                    $spotLimit->save();

                }
            }
        }
    }
    public function cancel(string $orderId, string $type)
{

    switch ($type) {
        case 'Market':
            $order = SpotMarket::findOrFail($orderId);
            break;
        case 'Limit':
            $order = SpotLimit::findOrFail($orderId);
            break;
        case 'StopLimit':
            $order = SpotStopLimit::findOrFail($orderId);
            break;
        default:
            $notify[] = ['error', 'Invalid order type'];
            return redirect()->back()->withNotify($notify);
    }
    $coinPair = CoinPair::where('id', $order->coin_pair_id)->first();
        $coinId1 = $coinPair->crypto_currency_id;
        $coinId2 = $coinPair->CryptoCurrency_id1;
    // if status is not open then return
    if ($order->status != 'open') {
        $notify[] = ['error', 'Invalid order status'];
        return redirect()->back()->withNotify($notify);
    }

    // if order is not mine then return
    if ($order->user_id != auth()->id()) {
        $notify[] = ['error', 'Invalid order'];
        return redirect()->back()->withNotify($notify);
    }

    // if order id does not exist then return
    if ($order == null) {
        $notify[] = ['error', 'Invalid order'];
        return redirect()->back()->withNotify($notify);
    }

    $order->status = 'cancelled';
    if($order->action == 'buy'){
        $wallet = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $coinId2)->first();
        $wallet->available_balance += $order->amount * $order->price;
        $wallet->save();
    }
    else{
        $wallet = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $coinId1)->first();
        $wallet->available_balance += $order->amount;
        $wallet->save();
    }
    $order->save();
    // $order->update(['status' => 'cancelled']);

    $notify[] = ['success', 'Order cancelled successfully'];
    return redirect()->back()->withNotify($notify);
}
}
