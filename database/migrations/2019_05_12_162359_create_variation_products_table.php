<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVariationProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variation_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->float('price_normal');
            $table->float('price_regular')->nullable();
            $table->integer('product_id');
            $table->integer('quantity')->default(0);
            $table->string('label');
            $table->integer('attached_id')->nullable();
            $table->mediumText('observation')->nullable();
            $table->string('weight')->nullable();
            $table->string('width')->nullable();
            $table->string('height')->nullable();
            $table->string('length')->nullable();
            $table->boolean('delivery')->default(0);
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
        Schema::dropIfExists('variation_products');
    }
}
