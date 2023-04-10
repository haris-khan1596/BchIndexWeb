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
        Schema::create('fake_order_books', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CoinPair::class);
            $table->enum('action', ['buy', 'sell']);
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
        Schema::dropIfExists('fake_order_books');
    }
};
