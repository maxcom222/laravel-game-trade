<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('countries', function (Blueprint $table) {
          $table->increments('id');
          $table->text('name');
          $table->string('native')->nullable();
          $table->string('code');
          $table->integer('parent_id')->unsigned()->nullable();
          $table->integer('lft')->unsigned()->nullable();
          $table->integer('rgt')->unsigned()->nullable();
          $table->integer('depth')->unsigned()->nullable();
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
        Schema::drop('countries');
    }
}
