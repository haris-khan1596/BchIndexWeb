<?php

namespace App\Console\Commands;
use App\Http\Controllers\Controller;
use App\Models\OrderBookSpot;
use App\Models\CoinPair;
use App\Models\CryptoCurrency;
use App\Models\spotWallet;
use App\Models\FakeSpotChart;
use Illuminate\Console\Command;

class FChart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'FChart:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $coin_pairs = CoinPair::all();

        foreach ($coin_pairs as $coin_pair) {
            $base_coin = CryptoCurrency::where('id', $coin_pair->crypto_currency_id)->first();
            $quote_coin = CryptoCurrency::where('id', $coin_pair->CryptoCurrency_id1)->first();

            $price = $base_coin->rate / $quote_coin->rate;


            if ($price < 1) {
                $val = 10000000000;
            } else {
                $val = 100;
            }

            $timestamp = FakeSpotChart::where('coin_pair_id', $coin_pair->id)->orderBy('timeframe', 'desc')->first()->timeframe + 60;

            $last_close = FakeSpotChart::where('coin_pair_id', $coin_pair->id)->orderBy('timeframe', 'desc')->first()->close;

            for ($i=0; $i < 6; $i++) {

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
        }


        // $timestamp=$coin_pair->created_at->timestamp;









        return Command::SUCCESS;
    }
}
