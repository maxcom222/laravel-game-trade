<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesListingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listings', function (Blueprint $table) {
            // IDs
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('game_id')->unsigned();
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');

            $table->string('name')->nullable();
            $table->string('picture')->nullable();
            $table->text('description')->nullable();
            $table->integer('price')->nullable();
            $table->integer('condition')->nullable();
            $table->integer('digital')->nullable();
            $table->string('limited_edition')->nullable();
            $table->boolean('delivery')->default('0');
            $table->integer('delivery_price')->nullable();
            $table->boolean('pickup')->default('0');

            $table->boolean('sell')->default('0');
            $table->boolean('sell_negotiate')->default('0');
            $table->boolean('trade')->default('0');
            $table->boolean('trade_negotiate')->default('0');
            $table->text('trade_list')->nullable();
            $table->boolean('payment')->default('0');
            $table->integer('status')->nullable();
            $table->integer('clicks')->default('0');
            $table->timestamp('last_offer_at');
            $table->softDeletes();
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
        Schema::drop('games_listings');
    }
}
