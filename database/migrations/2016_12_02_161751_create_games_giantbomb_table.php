<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesGiantbombTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_giantbomb', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('summary')->nullable();
            $table->text('genres')->nullable();
            $table->string('image')->nullable();
            $table->text('images')->nullable();
            $table->text('videos')->nullable();
            $table->text('ratings')->nullable();
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
        Schema::drop('games_giantbomb');
    }
}
