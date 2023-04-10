<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CoinPair;
use App\Models\TransactionCharge;
use App\Models\CryptoCurrency;
use App\Models\Admin;
// use App\Models\FakeOrderBook;
use App\Models\FakeSpotChart;
use App\Http\Controllers\Spot\ChartController;

class SpotTransactionChargeController extends Controller
{

    public function index()
    {
        $pageTitle = "Spot Trading";

        $transaction_charge_spot_to_p2p = TransactionCharge::where('id', '10')->first();
        $transaction_charge_p2p_to_spot = TransactionCharge::where('id', '11')->first();

        $transaction_charge_bot = TransactionCharge::where('id', '12')->first();

        $admin=Admin::where('id','1')->first();

        $crypto_currencies = CryptoCurrency::all();

        $coin_pairs = CoinPair::all();

        return view('admin.spotTrading',compact('pageTitle', 'coin_pairs', 'transaction_charge_spot_to_p2p', 'transaction_charge_p2p_to_spot', 'crypto_currencies','admin', 'transaction_charge_bot'));
    }


    public function add_pair(Request $request){
        $request->validate([
            'base_coin' => 'required',
            'quote_coin' => 'required',
        ]);
        if ($request->base_coin == $request->quote_coin) {
            $notify[] = ['error', 'Base Coin and Quote Coin can not be same'];
            return redirect('/admin/spotTrading')->withNotify($notify);
            //return redirect('/admin/spotTrading')->with('error','Base Coin and Quote Coin can not be same');
        }
        $base_code = CryptoCurrency::where('id', $request->base_coin)->first()->code;
        $quote_code = CryptoCurrency::where('id', $request->quote_coin)->first()->code;
        $name =$base_code . ' - ' . $quote_code;
        $reversed_name= $quote_code . ' - ' . $base_code;
        $pair1 = CoinPair::where('name',$name)->first();
        $pair2 = CoinPair::where('name',$reversed_name)->first();
        if ( $pair1!=null){
            $notify[] = ['error', 'Pair Already exists'];
            return redirect('/admin/spotTrading')->withNotify($notify);
            //return redirect('/admin/spotTrading')->with('error','Pair Already exists');
        }
        if ($pair2!=null) {
            $notify[] = ['error', 'Reversed Pair Already exists i.e. '.$pair2->name];
            return redirect('/admin/spotTrading')->withNotify($notify);
            //return redirect('/admin/spotTrading')->with('error','Reversed Pair Already exists i.e. '.$pair2->name );
        }


        $pair = new CoinPair();
        $pair->name = $name;
        $pair->crypto_currency_id = $request->base_coin;
        $pair->CryptoCurrency_id1 = $request->quote_coin;
        $pair->save();
        $coin_pair=CoinPair::where('name',$name)->first();

        ChartController::AddNewCandle($coin_pair->id);

        $timestamp=$coin_pair->created_at->timestamp;

        $price = CryptoCurrency::where('id', $request->base_coin)->first()->rate / CryptoCurrency::where('id', $request->quote_coin)->first()->rate;



        $last_close = $price;

        if ($price < 1) {
            $val = 10000000000;
        } else {
            $val = 100;
        }

        for ($i=0; $i < 10; $i++) {

            $fakeSpotChart = new FakeSpotChart();
            $fakeSpotChart->coin_pair_id = $coin_pair->id;

            if (rand(0, 1) > 0.5) {
            // increasing
            $open = $last_close;

            $close = rand($open * $val, $open * 1.02 * $val) / $val;

            if($close > $price * 1.06)
            {
                $close = rand($price * $val, $price * 1.02 * $val) / $val;
            }

            $high = rand($close * $val, $close * 1.01 * $val) / $val;
            $low = rand($open * 0.99 * $val, $open * $val) / $val;

            $last_close = $close;

            }
            else {
            // decreasing
            $open = $last_close;
            $close = rand($open * 0.98 * $val, $open * $val) / $val;


            if($close < $price * 0.94)
            {
                $close = rand($price * 0.98 * $val, $price * $val) / $val;
            }

            $high = rand($open * $val, $open * 1.01 * $val) / $val;
            $low = rand($close * 0.99 * $val, $close * $val) / $val;

            $last_close = $close;
            }
                $fakeSpotChart->open = $open;
                $fakeSpotChart->high = $high;
                $fakeSpotChart->low = $low;
                $fakeSpotChart->close = $close;

                $fakeSpotChart->timeframe = $timestamp;

                $fakeSpotChart->save();

                $timestamp = $timestamp + 60;

        }
        $notify[] = ['success','Pair Added Successfully'];
        return redirect('/admin/spotTrading')->withNotify($notify);
        //return redirect('/admin/spotTrading')->with('success','Pair Added Successfully');
    }



    public function del_pair(Request $request,string $id){
        $pair = CoinPair::find($id);
        $pair->delete();
        $notify[] = ['success','Pair Deleted Successfully'];
        return redirect('/admin/spotTrading')->withNotify($notify);
        //return redirect('/admin/spotTrading')->with('success','Pair Deleted Successfully');
    }

    public function update_pair(Request $request,string $id, string $status){
        $pair = CoinPair::find($id);

        if($status == "true")
        {
            $pair->fake = 1;
        }
        else
        {
            $pair->fake = 0;
        }
        $pair->save();
        $notify[] = ['success','Pair Updated Successfully'];
        return redirect('/admin/spotTrading')->withNotify($notify);
        //return redirect('/admin/spotTrading')->with('success','Pair Updated Successfully');
    }

    public function update_s2p(Request $request){
        $request->validate([
            'percentage'=>'required|numeric|min:0|max:100'
        ]);
        $transaction_charge_spot_to_p2p = TransactionCharge::where('id', '11')->first();
        $transaction_charge_spot_to_p2p->percent_charge=$request->percentage;
        $transaction_charge_spot_to_p2p->save();
        $notify[] = ['success','Charges Updated'];
        return redirect('/admin/spotTrading')->withNotify($notify);
        // return redirect('/admin/spotTrading')->with('success','Updated');
    }
    public function update_p2s(Request $request){
        $request->validate([
            'percentage'=>'required|numeric|min:0|max:100'
        ]);
        $transaction_charge_spot_to_p2p = TransactionCharge::where('id', '10')->first();
        $transaction_charge_spot_to_p2p->percent_charge=$request->percentage;
        $transaction_charge_spot_to_p2p->save();
        $notify[] = ['success','Charges Updated'];
        return redirect('/admin/spotTrading')->withNotify($notify);
        //return redirect('/admin/spotTrading')->with('success','Updated');
    }

    public function setBot(Request $request){
        $request->validate([
            'percentage'=>'required|numeric|min:0|max:100'
        ]);

        $transaction_charge_for_bot = TransactionCharge::where('id', '12')->first();
        $transaction_charge_for_bot->percent_charge=$request->percentage;
        $transaction_charge_for_bot->save();

        $notify[] = ['success','Charges Updated'];
        return redirect('/admin/spotTrading')->withNotify($notify);
        //return redirect('/admin/spotTrading')->with('success','Updated');
    }
}
