<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('users_id');
            $table->string('name');
            $table->string('address');
            $table->string('slug', 200);
            $table->string('zip_code',10);
            $table->string('legal_id',200);
            $table->string('email_corporate',200);
            $table->integer('image_cover');
            $table->integer('image_header');
            $table->string('phone_mobil',200)->nullable($value = true);
            $table->string('phone_fixed',200)->nullable($value = true);
            $table->string('meta_key',200);
            $table->string('terms_conditions')->nullable($value = true);
            $table->string('polity_privacy')->nullable($value = true);
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
        Schema::dropIfExists('shops');
    }
}
