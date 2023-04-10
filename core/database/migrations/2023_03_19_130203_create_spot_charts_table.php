<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\CoinPair;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spot_charts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('coin_pair_id')->constrained('coin_pairs');

            $table->double('open')->Nullable()->default(null);
            $table->double('high')->Nullable()->default(null);
            $table->double('low')->Nullable()->default(null);
            $table->double('close')->Nullable()->default(null);

            $table->integer('timeframe');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spot_charts');
    }
};
