<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_keys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('platform_payment');
            $table->enum('status', ['test', 'live'])->default($value = 'test');
            $table->string('app_id');
            $table->string('app_secret');
            $table->string('live_end_point');
            $table->string('sandbox_end_point');
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
        Schema::dropIfExists('payment_keys');
    }
}
