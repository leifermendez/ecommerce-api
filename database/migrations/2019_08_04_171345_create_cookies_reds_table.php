<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCookiesRedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cookies_reds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('users_id')->nullable();
            $table->longText('labels');
            $table->longText('src')->nullable();
            $table->longText('ip');
            $table->longText('browser');
            $table->longText('country');
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
        Schema::dropIfExists('cookies_reds');
    }
}
