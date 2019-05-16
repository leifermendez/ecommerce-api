<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',200);
            $table->string('short_description',200);
            $table->enum('featured', ['premium', 'regular','not'])->default($value = 'not');
            $table->enum('product_type', ['digital', 'physical'])->default($value = 'physical');
            $table->longText('description');
            $table->integer('user_id');
            $table->integer('category_id');
            $table->string('zip_code',10);
            $table->enum('status', ['available', 'unavailable'])->default($value = 'available');
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
        Schema::dropIfExists('products');
    }
}
