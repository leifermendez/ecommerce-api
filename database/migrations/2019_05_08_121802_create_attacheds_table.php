<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attacheds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 200);
            $table->integer('users_id');
            $table->string('small');
            $table->string('medium');
            $table->string('large');
            $table->string('original');
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
        Schema::dropIfExists('attacheds');
    }
}
