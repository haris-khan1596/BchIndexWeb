<?php

namespace App\Http\Controllers\Spot;

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
use Illuminate\Support\Facades\DB;

class OrderBookController extends Controller
{
    public function __construct()
    {
        return $this->activeTemplate = activeTemplate();
    }

    public function exchange()
    {
        $coinPairs = CoinPair::orderBy('id', 'asc')->get();
        $pair = $coinPairs[0]->id;

        if (auth()->check()) {
            return redirect('user/exchange/'.$pair);
        }
        else {
            return redirect('exchange/'.$pair);
        }

    }
    public function guestcreate(Request $request, string $pair)
    {

        $singlePair = CoinPair::where('id', $pair)->first();

        // only to be sent when fake spot charts have been enabled
        // send chart on the basis of the pair option whether fake or orignal has been selected.
        if ($singlePair->fake ==1){
            $chart = DB::table('fake_spot_charts')->select('open', 'high', 'low', 'close', DB::raw('timeframe as time'))->where('coin_pair_id', $pair)->where('timeframe', '<=', now()->timestamp)->orderBy('timeframe', 'asc')->get();
        }
        else{
            $chart = DB::table('spot_charts')->select('open', 'high', 'low', 'close', DB::raw('timeframe as time'))->where('coin_pair_id', $pair)->where('open','!=', null)->where('timeframe', '<=', now()->timestamp)->orderBy('timeframe', 'asc')->get();
        }

        $coinPairs = CoinPair::all();

        $cryptoCurrency1 =CryptoCurrency::where('id', $singlePair->crypto_currency_id)->first();
        $cryptoCurrency2 = CryptoCurrency::where('id', $singlePair->CryptoCurrency_id1)->first();


        $pageTitle = 'Exchange';
        $orderBook = SpotLimit::where('coin_pair_id', $pair)->where('status','open')->get();
        return view($this->activeTemplate . 'user.exchange', compact('pageTitle','pair','coinPairs', 'cryptoCurrency1', 'cryptoCurrency2', 'chart', 'singlePair',  'orderBook'));


    }

    public function create(Request $request, string $pair)
    {

        $singlePair = CoinPair::where('id', $pair)->first();

        // only to be sent when fake spot charts have been enabled
        // send chart on the basis of the pair option whether fake or orignal has been selected.
        if ($singlePair->fake ==1){
            $chart = DB::table('fake_spot_charts')->select('open', 'high', 'low', 'close', DB::raw('timeframe as time'))->where('coin_pair_id', $pair)->where('timeframe', '<=', now()->timestamp)->orderBy('timeframe', 'asc')->get();
        }
        else{
            $chart = DB::table('spot_charts')->select('open', 'high', 'low', 'close', DB::raw('timeframe as time'))->where('coin_pair_id', $pair)->where('open','!=', null)->where('timeframe', '<=', now()->timestamp)->orderBy('timeframe', 'asc')->get();
        }

        $coinPairs = CoinPair::all();

        $cryptoCurrency1 =CryptoCurrency::where('id', $singlePair->crypto_currency_id)->first();
        $cryptoCurrency2 = CryptoCurrency::where('id', $singlePair->CryptoCurrency_id1)->first();

        $cryptoBalance1 = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $singlePair->crypto_currency_id)->first()->available_balance ?? 0;

        $cryptoBalance2 = spotWallet::where('user_id', auth()->id())->where('crypto_currency_id', $singlePair->CryptoCurrency_id1)->first()->available_balance ?? 0;

        $pageTitle = 'Exchange';



        // $orderMarket = SpotMarket::where('coin_pair_id', $pair)->where('user_id', auth()->id())->where('coin_pair_id', $pair);
        // $orderLimit = SpotLimit::where('coin_pair_id', $pair)->where('user_id', auth()->id())->where('coin_pair_id', $pair);
        // $orderStopLimit = SpotStopLimit::where('coin_pair_id', $pair)->where('user_id', auth()->id())->where('coin_pair_id', $pair);

        // $allOrders = $orderMarket->union($orderLimit)->union($orderStopLimit)->get();

        $orderMarket = SpotMarket::select('id', 'created_at', 'coin_pair_id', 'action', 'status', 'price', 'amount', 'filled', DB::raw("'Market' as type"))->where('coin_pair_id', $pair)->where('user_id', auth()->id());

        $orderLimit = SpotLimit::select('id', 'created_at', 'coin_pair_id', 'action', 'status', 'price', 'amount', 'filled', DB::raw("'Limit' as type"))->where('coin_pair_id', $pair)->where('user_id', auth()->id());

        $orderStopLimit = SpotStopLimit::select('id', 'created_at', 'coin_pair_id', 'action', 'status', 'price', 'amount', 'filled', DB::raw("'StopLimit' as type"))->where('coin_pair_id', $pair)->where('user_id', auth()->id());

        $allOrders = $orderMarket->union($orderLimit)->union($orderStopLimit)->orderBy('created_at', 'desc')->get();

        $orderBook = SpotLimit::where('coin_pair_id', $pair)->where('status','open')->get();




        return view($this->activeTemplate . 'user.exchange', compact('pageTitle','pair','coinPairs', 'cryptoCurrency1', 'cryptoCurrency2', 'cryptoBalance1', 'cryptoBalance2', 'chart', 'singlePair', 'allOrders', 'orderBook'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function changeCoinPair(Request $request)
    {
        $pair = $request->pair;
        if (auth()->check()) {
            return redirect('user/exchange/'.$pair);
        }
        else {
            return redirect('exchange/'.$pair);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'coinpair'=>'required|numeric',
            'action'=>'required',
            'amount'=>'required|numeric',
            'price'=>'required|numeric',
        ]);

        $order = new OrderBookSpot();
        $order->coin_pair_id=$request->coinpair;
        $order->action=$request->action;
        $order->amount=$request->amount;
        $order->price=$request->price;
        $order->save();
        return redirect('user/exchange/'.$request->coinpair);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrderBookSpot  $orderBookSpot
     * @return \Illuminate\Http\Response
     */
    // public function show(OrderBookSpot $orderBookSpot)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OrderBookSpot  $orderBookSpot
     * @return \Illuminate\Http\Response
     */
    // public function edit(OrderBookSpot $orderBookSpot)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OrderBookSpot  $orderBookSpot
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, OrderBookSpot $orderBookSpot)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrderBookSpot  $orderBookSpot
     * @return \Illuminate\Http\Response
     */
    // public function destroy(OrderBookSpot $orderBookSpot)
    // {
    //     $order=OrderBookController::find($orderBookSpot);
    //     if ($order == null) {
    //         return redirect()->back()->with('error','Not a valid order');
    //     }
    //     $order->destroy();
    //     return redirect()->back()->with('success');
    // }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrderBookSpot  $orderBookSpot
     * @return \Illuminate\Http\Response
     */

    public function orderBookUpdate(Request $request,string $pair)
    {
        $orderBook = SpotLimit::where('coin_pair_id', $pair)->where('status','open')->get();
        return response()->json($orderBook);
    }

    public function GetData(Request $request, string $pair, string $time)
    {
        // convert string to int of 8 byte



        $coinpair = CoinPair::where('id', $pair)->first();
        if ($coinpair->fake ==1){
            $time = (int)$time + 60;
            $chart = DB::table('fake_spot_charts')->select('open', 'high', 'low', 'close', DB::raw('timeframe as time'))->where('coin_pair_id', $pair)->where('timeframe', $time)->orderBy('timeframe', 'desc')->get();
        } else {
            $time = (int)$time + 300;
            $chart = DB::table('spot_charts')->select('open', 'high', 'low', 'close', DB::raw('timeframe as time'))->where('coin_pair_id', $pair)->where('timeframe', $time)->orderBy('timeframe', 'desc')->get();
        }
        return response()->json($chart);
    }



}
