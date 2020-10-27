<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameTradeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_trade', function (Blueprint $table) {
          $table->integer('listing_id')->unsigned()->index();
          $table->foreign('listing_id')->references('id')->on('listings')->onDelete('cascade');
          $table->integer('listing_game_id')->unsigned()->index();
          $table->foreign('listing_game_id')->references('id')->on('games')->onDelete('cascade');
          $table->integer('game_id')->unsigned()->index();
          $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
          $table->string('price_type')->nullable();
          $table->integer('price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_trade');
    }
}
