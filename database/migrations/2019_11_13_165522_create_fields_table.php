<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// phpcs:ignore
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
            $table->text('options')->nullable();
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

            $table->primary(['field_id', 'model_type', 'model_id']);
        });

        Schema::create('fields_values', function (Blueprint $table) {
            $table->bigInteger('field_id')->unsigned();
            $table->bigInteger('model_id')->unsigned();
            $table->string('model_type');
            $table->text('value');

            $table->index(['model_type', 'model_id']);

            $table->foreign('field_id')
                ->references('id')
                ->on('fields')
                ->onDelete('cascade');

            $table->primary(['field_id', 'model_type', 'model_id']);
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
