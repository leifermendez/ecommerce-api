<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketSupportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_supports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('purchase_id');
            $table->integer('user_id');
            $table->integer('reply_user_id');
            $table->integer('attached_id')->nullable();
            $table->enum('status', ['close', 'open']);
            $table->string('comment');
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
        Schema::dropIfExists('ticket_supports');
    }
}
