<?php

namespace App\Http\Controllers;

use App\Models\CryptoCurrency;
use App\Models\FiatCurrency;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Spot\ChartController;
use App\Models\CoinPair;


class CronController extends Controller
{
    public function fiatRate()
    {
        Log::info('fiatRate cron has just started...');
        $general      = gs();

        $general->fiat_cron = Carbon::now();
        $general->save();
        $fiatcurr = FiatCurrency::Active()->pluck('code')->toArray();

        $fiatcurr = implode(',', $fiatcurr);

        $parameters = [
            'symbol' => $fiatcurr,
        ];
        $endpoint     = 'live';
        $access_key   = $general->fiat_api_key;
        $baseCurrency = 'USD';

        $url = 'https://api.apilayer.com/currency_data/live?base='.$baseCurrency.'';

        $qs      = http_build_query($parameters); // query string encode the parameters
        $request = "{$url}&{$qs}"; // create the request URL

        //$ch           = curl_init('http://apilayer.net/api/' . $endpoint . '?access_key=' . $access_key . '&source=' . $baseCurrency);
        $ch           = curl_init($request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apikey:'.$access_key.''
        ]);
        $json = curl_exec($ch);
        curl_close($ch);
        $exchangeRates = json_decode($json);

        if ((isset($exchangeRates->success) && $exchangeRates->success == false) || (isset($exchangeRates->message) && $exchangeRates->message != '')) {
            if(!empty($exchangeRates->message)){
                $errorMsg = $exchangeRates->message;
                echo "Error: $errorMsg";
                Log::info('fiatRate cron error: '.$errorMsg);
            }else {
                $errorMsg = $exchangeRates->error->info;
                echo "Error: $errorMsg";
                Log::info('fiatRate cron error: ' . $errorMsg);
            }
            echo "<br>Run with error, check logs file!<br>";
            die('EXECUTED');
        } else {
            foreach ($exchangeRates->quotes as $key => $rate) {
                $curcode  = substr($key, -3);
                $currency = FiatCurrency::where('code', $curcode)->first();
                if ($currency) {
                    $currency->rate = $rate;
                    $currency->save();
                }
            }
            Log::info('fiatRate cron run with success!');
            echo "<br>fiatRate cron run with success!<br>";
            die('EXECUTED');
        }
    }

    public function cryptoRate()
    {
        Log::info('cryptoRate cron has just started...');
        
        $general = gs();

        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest';
        $cryptos = CryptoCurrency::where('status','=',1)->pluck('code')->toArray();

        $cryptos = implode(',', $cryptos);

        $parameters = [
            'symbol' => $cryptos,
            'convert' => 'USD',
        ];
        $headers = [
            'Accepts: application/json',
            'X-CMC_PRO_API_KEY:' . trim($general->crypto_api_key),
        ];
        $qs      = http_build_query($parameters); // query string encode the parameters
        $request = "{$url}?{$qs}"; // create the request URL

        $curl    = curl_init(); // Get cURL resource
        // Set cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $request, // set the request URL
            CURLOPT_HTTPHEADER     => $headers, // set the headers
            CURLOPT_RETURNTRANSFER => 1, // ask for raw response instead of bool
        ));
        $response = curl_exec($curl); // Send the request, save the response

        curl_close($curl); // Close request

        $a = json_decode($response);
        
        
        if (isset($a->status) && $a->status->error_code!=0) {
            Log::info($a->status->error_message);
            Log::info('cryptoRate cron error....');
            echo $a->status->error_message;
            die('done');
        }

        $coins = $a->data;
        if(isset($coins)){
            foreach ($coins as $coin) {
                //dd($coin);
                $currency = CryptoCurrency::where('code', $coin->symbol)->first();
                if ($currency) {
                    $defaultCurrency = 'USD';
                    if($coin->symbol==='USDT'){
                        $currency->rate = getAmount($coin->quote->$defaultCurrency->price,2);
                    }else{
                        $currency->rate = $coin->quote->$defaultCurrency->price;
                    }
                    $currency->save();
                }
            }
        }
        
        
        $general->crypto_cron = Carbon::now();
        $general->save();
        
        Log::info('cryptoRate cron run with success!');
        echo "<br>cryptoRate cron run with success!<br>";
        die('EXECUTED');
        
    }

    public function fakeChart()
    {
        Artisan::call('FChart:cron');
        
        // iterate on each CoinPair and call ChartController::AddNewCandle()
        $coinPairs = CoinPair::all();

        foreach ($coinPairs as $coinPair) {
            ChartController::AddNewCandle($coinPair->id);
        }

    }

    public function DelFakeChart()
    {
        Artisan::call('FChart:del');
    }

    
}
