<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformDigitalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('digital_platform', function (Blueprint $table) {
            $table->integer('platform_id')->unsigned()->index();
            $table->foreign('platform_id')->references('id')->on('platforms')->onDelete('cascade');
            $table->integer('digital_id')->unsigned()->index();
            $table->foreign('digital_id')->references('id')->on('digitals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('platform_digital');
    }
}
