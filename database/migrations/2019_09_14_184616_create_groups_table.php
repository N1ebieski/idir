<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('model_type');
            $table->string('slug')->unique();
            $table->string('name')->unique();
            $table->text('desc')->nullable();
            $table->string('border')->nullable();
            $table->integer('max_cats')->unsigned();
            $table->integer('position')->unsigned();
            $table->integer('visible')->unsigned();
            $table->integer('backlink')->unsigned();
            $table->integer('days')->unsigned()->nullable();
            $table->integer('max_dirs')->unsigned()->nullable();
            $table->timestamps();

            $table->index(['id', 'model_type']);
        });

        Schema::create('privileges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('groups_privileges', function (Blueprint $table) {
            $table->bigInteger('group_id')->unsigned();
            $table->bigInteger('privilege_id')->unsigned();

            $table->foreign('group_id')
                ->references('id')
                ->on('groups')
                ->onDelete('cascade');

            $table->primary(['group_id', 'privilege_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups');
        Schema::dropIfExists('privileges');
        Schema::dropIfExists('groups_privileges');
    }
}
