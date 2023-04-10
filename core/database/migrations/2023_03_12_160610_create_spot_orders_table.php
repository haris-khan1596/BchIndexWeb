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
        Schema::create('spot_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CoinPair::class);
            $table->enum('type',['limit','market','stop-limit']);
            $table->enum('action', ['buy', 'sell']);
            $table->enum('status', ['inprogress', 'completed', 'cancelled']);
            $table->double('amount');
            $table->double('price');
            $table->double('filled')->default(0);
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
        Schema::dropIfExists('spot_orders');
    }
};
