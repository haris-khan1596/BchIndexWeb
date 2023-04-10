<?php

namespace App\Http\Controllers\Spot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SpotChart;

class ChartController extends Controller
{
    public static function AddNewCandle(string $pair)
    {
        $lastCandle = SpotChart::where('coin_pair_id', $pair)->orderBy('timeframe', 'desc')->first();

        $spotChart = new SpotChart();
        $spotChart->coin_pair_id = $pair;

        // Adding first candle
        if($lastCandle == null){
            $spotChart->timeframe = now()->timestamp;
            $spotChart->save();
            return;
        }


        $spotChart->timeframe = $lastCandle->timeframe + 300;
        $spotChart->save();
    }

    public static function UpdateCandle($pair, $price)
    {



        $lastCandle = SpotChart::where('coin_pair_id', $pair)->where('timeframe', '<=', now()->timestamp)->orderBy('timeframe', 'desc')->first();

        if($lastCandle == null){
            self::AddNewCandle($pair);
            $lastCandle = SpotChart::where('coin_pair_id', $pair)->where('timeframe', '<=', now()->timestamp)->orderBy('timeframe', 'desc')->first();
        }

        if($lastCandle->open == null){
            $lastCandle->open = $price;
            $lastCandle->high = $price;
            $lastCandle->low = $price;
            $lastCandle->close = $price;
            $lastCandle->save();
        }
        else
        {
            if($price > $lastCandle->high){
                $lastCandle->high = $price;
            }
            if($price < $lastCandle->low){
                $lastCandle->low = $price;
            }
            $lastCandle->close = $price;
            $lastCandle->save();
        }
    }
}
