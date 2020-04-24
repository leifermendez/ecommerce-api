<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('attached_id');
            $table->integer('attached_responsive_id')->nullable();
            $table->integer('shop_id')->nullable();
            $table->string('title');
            $table->string('description');
            $table->enum('media_type', ['video', 'image'])->default('image');
            $table->timestamp('start')->nullable();
            $table->timestamp('finish')->nullable();
            $table->string('url');
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
        Schema::dropIfExists('banners');
    }
}
