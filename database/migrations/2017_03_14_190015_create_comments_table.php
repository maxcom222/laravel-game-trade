<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
         Schema::create('comments', function (Blueprint $table) {
             $table->increments('id');
             $table->integer('commentable_id');
             $table->string('commentable_type');
             $table->integer('user_id')->unsigned();
             $table->foreign('user_id')->references('id')->on('users');
             $table->text('content');
             $table->integer('likes')->default('0');
             $table->integer('status')->nullable();
             $table->boolean('has_children')->nullable();
             $table->integer('root_id')->nullable();
             $table->timestamp('last_reply_at')->nullable();
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
        //
    }
}
