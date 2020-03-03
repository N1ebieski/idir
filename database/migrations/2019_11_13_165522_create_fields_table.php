<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * [CreateFieldsTable description]
 */
class CreateFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('model_type');
            $table->string('title');
            $table->text('desc')->nullable();
            $table->string('type')->index();
            $table->boolean('visible');
            $table->json('options')->nullable();
            $table->integer('position')->unsigned();
            $table->timestamps();

            $table->index(['id', 'model_type']);
        });

        // Full Text Index
        DB::statement('ALTER TABLE `fields` ADD FULLTEXT fulltext_index (title)');

        Schema::create('fields_models', function (Blueprint $table) {
            $table->bigInteger('field_id')->unsigned();
            $table->bigInteger('model_id')->unsigned();
            $table->string('model_type');
            $table->index(['model_type', 'model_id']);

            $table->foreign('field_id')
                ->references('id')
                ->on('fields')
                ->onDelete('cascade');

            $table->primary(['field_id', 'model_type', 'model_id'], 'fields_primary');
        });

        Schema::create('fields_values', function (Blueprint $table) {
            $table->bigInteger('field_id')->unsigned();
            $table->bigInteger('model_id')->unsigned();
            $table->string('model_type');
            $table->json('value');

            $table->index(['model_type', 'model_id']);

            $table->foreign('field_id')
                ->references('id')
                ->on('fields')
                ->onDelete('cascade');

            $table->primary(['field_id', 'model_type', 'model_id'], 'fields_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fields');
        Schema::dropIfExists('fields_models');
        Schema::dropIfExists('fields_values');
    }
}
