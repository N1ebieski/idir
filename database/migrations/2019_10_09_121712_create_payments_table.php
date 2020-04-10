<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * [CreatePaymentsTable description]
 */
class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->bigInteger('model_id')->unsigned()->nullable();
            $table->string('model_type')->nullable();
            $table->bigInteger('order_id')->unsigned();
            $table->string('order_type');
            $table->tinyInteger('status')->unsigned();
            $table->longText('logs')->nullable();
            $table->string('driver');
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
            $table->index(['order_type', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
