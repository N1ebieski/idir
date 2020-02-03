<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug')->index();
            $table->string('name');
            $table->timestamps();           
        });

        Schema::create('regions_models', function (Blueprint $table) {
            $table->bigInteger('region_id')->unsigned();
            $table->bigInteger('model_id')->unsigned();
            $table->string('model_type');

            $table->index(['model_type', 'model_id']);

            $table->foreign('region_id')
                ->references('id')
                ->on('regions')
                ->onDelete('cascade');

            $table->primary(['region_id', 'model_type', 'model_id'],
                    'regions_primary');
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('regions');
        Schema::dropIfExists('regions_models');
    }
}
