<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spot_stop_limits', function (Blueprint $table) {
            $table->id();
            $table->ForeignId('user_id')->constrained('users');
            $table->ForeignId('coin_pair_id')->constrained('coin_pairs');
            $table->double('price');
            $table->double('amount');
            $table->enum('action', ['buy', 'sell']);
            $table->enum('status', ['open', 'placed', 'cancelled']);
            $table->double('filled')->default(0);
            $table->double('stop_price');
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
        Schema::dropIfExists('spot_stop_limits');
    }
};
