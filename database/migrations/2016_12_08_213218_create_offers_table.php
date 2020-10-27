<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('status')->default('0');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

            $table->integer('listing_id')->unsigned();

            $table->integer('thread_id')->unsigned()->nullable();

            $table->text('note')->nullable();

            $table->integer('price_offer')->nullable();
            $table->integer('trade_game')->nullable()->unsigned();
            $table->string('additional_type')->nullable();
            $table->integer('additional_charge')->nullable();

            $table->boolean('delivery')->default('1');

            $table->boolean('trade_from_list')->nullable();
            $table->boolean('declined')->default('0');
            $table->text('decline_note')->nullable();
            $table->integer('rating_id_offer')->nullable();
            $table->integer('rating_id_listing')->nullable();
            $table->timestamp('closed_at')->nullable();
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
        Schema::drop('offers');
    }
}
