<?php

namespace App\Http\Controllers\Spot\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderBookSpot;
use App\Models\CoinPair;
use App\Models\CryptoCurrency;
use App\Models\spotWallet;
use Illuminate\Http\Request;
use App\Models\FakeSpotChart;

use App\Models\SpotLimit;
use App\Models\SpotMarket;
use App\Models\SpotStopLimit;
use App\Models\SpotChart;
use App\Http\Controllers\Spot\OrderController;
use App\Http\Controllers\Spot\ChartController;
use Illuminate\Support\Facades\DB;

class SpotOrdersController extends Controller
{

    public function __construct()
    {

    }

    public function pairs()
    {
        $coinPairs = CoinPair::orderBy('id', 'asc')->get();
        $coinPairsList=[];
        foreach ($coinPairs as $coinPair){
            $coinPairsList[$coinPair->id] = $coinPair->name;
        }
        return response()->json([
            'coinPairs' => [$coinPairsList]
        ]);
// {1: "BTC/USDT", 2: "ETH/USDT", 3: "XRP/USDT", 4: "BCH/USDT", 5: "LTC/USDT", 6: "EOS/USDT", 7: "BNB/USDT", 8: "TRX/USDT", 9: "XLM/USDT", 10: "ADA/USDT", 11: "XMR/USDT", 12: "DASH/USDT", 13: "ZEC/USDT", 14: "ETC/USDT", 15: "QTUM/USDT", 16: "NEO/USDT", 17: "OMG/USDT", 18: "ZRX/USDT", 19: "BAT/USDT", 20: "LINK/USDT", 21: "REP/USDT", 22: "KNC/USDT", 23: "GNT/USDT", 24: "LOOM/USDT", 25: "ZIL/USDT", 26: "BNT/USDT", 27: "WTC/USDT", 28: "MANA/USDT", 29: "SNT/USDT", 30: "CVC/USDT", 31: "MCO/USDT", 32: "GAS/USDT", 33: "STORJ/USDT", 34: "KMD/USDT", 35: "RCN/USDT", 36: "NULS/USDT", 37: "PPT/USDT", 38: "WAVES/USDT", 39: "BCPT/USDT", 40: "ARN/USDT", 41: "GVT/USDT", 42: "CDT/USDT", 43: "GXS/USDT", 44: "POWR/USDT", 45: "ENG/USDT", 46: "AION/USDT", 47: "REQ/USDT", 48: "VIB/USDT", 49: "RLC/USDT", 50: "INS/USDT", 51: "RDN/US}
    }

    public function create(Request $request, string $pair){
        $singlePair = CoinPair::where('id', $pair)->first();

        // only to be sent when fake spot charts have been enabled
        // send chart on the basis of the pair option whether fake or orignal has been selected.
        if ($singlePair->fake ==1){
            $chart = DB::table('fake_spot_charts')->select('open', 'high', 'low', 'close', DB::raw('timeframe as time'))->where('coin_pair_id', $pair)->where('timeframe', '<=', now()->timestamp)->orderBy('timeframe', 'asc')->take(200)->get();
        }
        else{
            $chart = DB::table('spot_charts')->select('open', 'high', 'low', 'close', DB::raw('timeframe as time'))->where('coin_pair_id', $pair)->where('open','!=', null)->where('timeframe', '<=', now()->timestamp)->orderBy('timeframe', 'asc')->get();
        }

        $coinPairs = CoinPair::all();

        $cryptoCurrency1 =CryptoCurrency::where('id', $singlePair->crypto_currency_id)->first();
        $cryptoCurrency2 = CryptoCurrency::where('id', $singlePair->CryptoCurrency_id1)->first();

        $price=$cryptoCurrency1->rate / $cryptoCurrency2->rate;

        $cryptoBalance1 = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $singlePair->crypto_currency_id)->first()->available_balance ?? 0;

        $cryptoBalance2 = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $singlePair->CryptoCurrency_id1)->first()->available_balance ?? 0;

        $orderMarket = SpotMarket::select('id', 'created_at', 'coin_pair_id', 'action', 'status', 'price', 'amount', 'filled', DB::raw("'Market' as type"))->where('coin_pair_id', $pair)->where('user_id', auth()->id());

        $orderLimit = SpotLimit::select('id', 'created_at', 'coin_pair_id', 'action', 'status', 'price', 'amount', 'filled', DB::raw("'Limit' as type"))->where('coin_pair_id', $pair)->where('user_id', auth()->id());
        $orderStopLimit = SpotStopLimit::select('id', 'created_at', 'coin_pair_id', 'action', 'status', 'price', 'amount', 'filled', DB::raw("'StopLimit' as type"))->where('coin_pair_id', $pair)->where('user_id', auth()->id());

        $AllOrders = $orderMarket->union($orderLimit)->union($orderStopLimit)->orderBy('created_at', 'desc')->get();

        $allOrders = array();
        foreach ($AllOrders as $order){
            $oneOrder = ['id'=>$order->id ,'action' => $order->action, 'status' => $order->status, 'price' => $order->price, 'amount' => $order->amount+$order->filled, 'filled' => $order->filled/($order->amount+$order->filled)*100, 'type' => $order->type,'total' => ($order->amount+$order->filled)*$order->price];
            array_push($allOrders,$oneOrder);
        }

        $orderBook = SpotLimit::where('coin_pair_id', $pair)->where('status','open')->get();
        return response()->json([
            'coinPairs' => $coinPairs,
            'CurrentPair' => $singlePair,
            'BaseCurrency' => $cryptoCurrency1,
            'QuoteCurrency' => $cryptoCurrency2,
            'BaseBalance' => $cryptoBalance1,
            'QuoteBalance' => $cryptoBalance2,
            'chart' => $chart,
            'Orders' => $allOrders,
            'orderBook' => $orderBook,
            'price' => $price
        ]);
    }

    public function spotLimitOrder(Request $request){
        $this->validate($request, [
            'pair' => 'required | numeric | exists:coin_pairs,id',
            'action' => 'required | in:buy,sell',
            'price' => 'required | numeric | min:0',
            'amount' => 'required | numeric | min:0',
        ]);

        if($request->amount<=0){
            return response()->json([
                'status' => 'error',
                'message' => 'Amount cannot be 0'
            ]);
        }
        if($request->price<=0){
            return response()->json([
                'status' => 'error',
                'message' => 'Price cannot be 0'
            ]);
        }

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
                        $openOrders += $spotMarketOrder->amount * $closePrice;
                    }

            // whole block is dedicated to find if user has enough balance to buy even if he has placed some open orders

            $wallet = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $quoteCoinId)->first();

            if ($wallet!=null) {
                $balance = $wallet->available_balance;

                $balance -= $openOrders;

                if ($balance < $request->amount * $request->price) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Insufficient balance',
                    ]);
                }
                else{
                    $wallet->available_balance -= $request->amount * $request->price;
                }
            }
            else {
                $msg = 'Wallet of '.CryptoCurrency::where('id',$quoteCoinId)->first()->code .' not found';
                return response()->json([
                    'status' => 'error',
                    'message' => 'Wallet of '.CryptoCurrency::where('id',$quoteCoinId)->first()->code .' not found',
                ]);
            }
        }
        else{
            $wallet = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $baseCoinId)->first();
            if($wallet!=null){
                $balance = $wallet->available_balance;
                if($balance < $request->amount){
                    return response()->json([
                        'status' => 'error',
                        'message' =>'Insufficient balance',
                    ]);
                }
                else{
                    $wallet->available_balance -= $request->amount;
                }
            }
            else{
                return response()->json([
                    'status' => 'error',
                    'message' => 'Wallet of '.CryptoCurrency::where('id',$baseCoinId)->first()->code .' not found',
                ]);
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
        
        return response()->json([
            'status' => 'Success',
            'message' => 'Order placed successfully',
        ]);

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
            // return back()->withNotify($notify);
            return response()->json([
                'status' => 'error',
                'message' => 'Amount cannot be 0'
            ]);
        }
        if($request->price<=0){
            $notify[] = ['error', 'Price cannot be 0'];
            // return back()->withNotify($notify);
            return response()->json([
                'status' => 'error',
                'message' => 'Price cannot be 0'
            ]);
        }
        if($request->stop_price<=0){
            $notify[] = ['error', 'Stop Price cannot be 0'];
            // return back()->withNotify($notify);
            return response()->json([
                'status' => 'error',
                'message' => 'Stop Price cannot be 0'
            ]);
        }

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
                        $openOrders += $spotMarketOrder->amount * $closePrice;
                    }

            // whole block is dedicated to find if user has enough balance to buy even if he has placed some open orders

            $wallet = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $quoteCoinId)->first();
            if ($wallet!=null) {
                $balance = $wallet->available_balance;

                $balance -= $openOrders;

                if ($balance < $request->amount * $request->price) {
                    $notify[] = ['error', 'Insufficient balance'];
                    // return back()->withNotify($notify);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Insufficient balance'
                    ]);
                }
                else{
                    $wallet->available_balance -= $request->amount * $request->price;
                }
            }
            else {
                $notify[] = ['error', 'Wallet of '.CryptoCurrency::where('id',$quoteCoinId)->first()->code .' not found'];
                // return back()->withNotify($notify);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Wallet of '.CryptoCurrency::where('id',$quoteCoinId)->first()->code .' not found'
                ]);
            }
        }
        else{
            $wallet = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $baseCoinId)->first();
            if($wallet!=null){
                $balance = $wallet->available_balance;
                if($balance < $request->amount){
                    $notify[] = ['error', 'Insufficient balance'];
                    // return back()->withNotify($notify);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Insufficient balance'
                    ]);
                }
                else{
                    $wallet->available_balance -= $request->amount;
                }
            }
            else{
                $notify[] = ['error', 'Wallet of '.CryptoCurrency::where('id',$baseCoinId)->first()->code .' not found'];
                // return back()->withNotify($notify);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Wallet of '.CryptoCurrency::where('id',$baseCoinId)->first()->code .' not found'
                ]);
            }
        }

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
        // return back()->withNotify($notify);
        return response()->json([
            'status' => 'Success',
            'message' => 'Order placed successfully'
        ]);
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
            // return back()->withNotify($notify);
            return response()->json([
                'status' => 'error',
                'message' => 'Amount cannot be 0'
            ]);
        }

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
                                $openOrders += $spotMarketOrder->amount * $closePrice;
                            }

                    // whole block is dedicated to find if user has enough balance to buy even if he has placed some open orders

                    $wallet = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $quoteCoinId)->first();
                    if ($wallet!=null) {
                        $balance = $wallet->available_balance;

                        $balance -= $openOrders;

                        if ($balance < $request->amount * $closePrice) {
                            $notify[] = ['error', 'Insufficient balance'];
                            // return back()->withNotify($notify);
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Insufficient balance'
                            ]);
                        }

                    }
                    else {
                        $notify[] = ['error', 'Wallet of '.CryptoCurrency::where('id',$quoteCoinId)->first()->code .' not found'];
                        // return back()->withNotify($notify);
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Wallet of '.CryptoCurrency::where('id',$quoteCoinId)->first()->code .' not found'
                        ]);

                    }
                }
                else{
                    // sell market order

                    $wallet = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $baseCoinId)->first();
                    if($wallet!=null){
                        $balance = $wallet->available_balance;
                        if($balance < $request->amount){
                            $notify[] = ['error', 'Insufficient balance'];
                            // return back()->withNotify($notify);
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Insufficient balance'
                            ]);
                        }
                        else{
                            $wallet->available_balance -= $request->amount;
                        }
                    }
                    else{
                        $notify[] = ['error', 'Wallet of '.CryptoCurrency::where('id',$baseCoinId)->first()->code .' not found'];
                        // return back()->withNotify($notify);
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Wallet of '.CryptoCurrency::where('id',$baseCoinId)->first()->code .' not found'
                        ]);
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

                        $Mwallet2->balance -= $spotLimit->amount * $spotLimit->price;
                        $Mwallet2->available_balance -= $spotLimit->amount * $spotLimit->price;
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

                        $Mwallet2->balance -= $spotMarket->amount * $spotLimit->price;
                        $Mwallet2->available_balance -= $spotMarket->amount * $spotLimit->price;
                        $Mwallet2->save();

                        $spotMarket->price = $spotLimit->price;

                        ChartController::UpdateCandle($request->pair, $spotLimit->price);
                        $this->CronStopLimitOrders($request->pair, $spotLimit->price);

                        $spotLimit->save();
                        $spotMarket->amount = 0;
                        $spotMarket->status = 'closed';
                        $spotMarket->save();
                        $notify[] = ['success', 'Order completed successfully'];
                        // return back()->withNotify($notify);
                        return response()->json([
                            'status' => 'success', 
                            'message' => 'Order completed successfully'
                        ]);
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

                        $Mwallet2->balance += $spotLimit->amount * $spotLimit->price;
                        $Mwallet2->available_balance += $spotLimit->amount * $spotLimit->price;
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

                        $Mwallet2->balance += $spotMarket->amount * $spotLimit->price;
                        $Mwallet2->available_balance += $spotMarket->amount * $spotLimit->price;
                        $Mwallet2->save();

                        $spotMarket->price = $spotLimit->price;

                        ChartController::UpdateCandle($request->pair, $spotLimit->price);
                        $this->CronStopLimitOrders($request->pair, $spotLimit->price);

                        $spotLimit->save();
                        $spotMarket->amount = 0;
                        $spotMarket->status = 'closed';
                        $spotMarket->save();
                        $notify[] = ['success', 'Order completed successfully'];
                        // return back()->withNotify($notify);
                        return response()->json([
                            'status' => 'success', 
                            'message' => 'Order completed successfully'
                        ]);
                    }
            }
        }

        $spotMarket->save();
        $notify[] = ['success', 'Order placed successfully'];
        // return back()->withNotify($notify);
        return response()->json([
            'status' => 'success', 
            'message' => 'Order placed successfully'
        ]);
    }

    public function cancel(Request $request, string $orderId,string $type)
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
        return response()->json([
            'status'=>'Failed',
            'message'=>'Invalid order status',
        ]);
    }

    // if order is not mine then return
    if ($order->user_id != auth()->id()) {
        $notify[] = ['error', 'Invalid order'];
        return response()->json([
                'status'=>'Failed',
                'message'=>'Invalid order',
            ]);
    }

    // if order id does not exist then return
    if ($order == null) {
        $notify[] = ['error', 'Invalid order'];
        return response()->json([
                'status'=>'Failed',
                'message'=>'Invalid order',
            ]);
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
    return response()->json([
        'status'=>'Success',
        'message'=>'Order cancelled successfully',
    ]);
}

}
