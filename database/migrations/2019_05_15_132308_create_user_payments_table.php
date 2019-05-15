<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->enum('payment_option', 
            [
                'bank',
                'paypal',
                'mercadopago',
                'other'
             ])->default('bank');
            $table->string('payment_email');
            $table->string('iban')->nullable();
            $table->longText('observation')->nullable();
            $table->string('account_name');
            $table->string('account_lastname');
            $table->integer('attached_id')->nullable();
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
        Schema::dropIfExists('user_payments');
    }
}
