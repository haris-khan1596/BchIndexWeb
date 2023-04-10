<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FakeSpotChart;
// use App\Models\SpotChart;
use App\Models\CoinPair;

class DeleteFcharts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'FChart:del';

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

        $coinpairs = CoinPair::all();
        foreach ($coinpairs as $coinpair) {
          
            $keep = FakeSpotChart::where('coin_pair_id', $coinpair->id)->orderBy('id', 'desc')->skip(1000)->first()->id;
            FakeSpotChart::where('coin_pair_id', $coinpair->id)->where('id', '<', $keep)->delete();
        }

        return Command::SUCCESS;
    }
}
