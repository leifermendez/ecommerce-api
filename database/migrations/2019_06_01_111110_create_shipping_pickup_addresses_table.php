<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippingPickupAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_pickup_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('shop_id')->unique();
            $table->mediumText('country');
            $table->mediumText('state');
            $table->mediumText('district');
            $table->longText('address');
            $table->string('zip_code');
            $table->mediumText('instructions');
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
        Schema::dropIfExists('shipping_pickup_addresses');
    }
}
