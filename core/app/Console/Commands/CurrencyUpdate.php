<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CronController;

class CurrencyUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:currency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update All cryptoRate & fiatRate';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //CronController::fiatRate();
        //CronController::cryptoRate();
        return Command::SUCCESS;
    }
}
