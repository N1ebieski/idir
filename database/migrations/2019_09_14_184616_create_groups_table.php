<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * [CreateGroupsTable description]
 */
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
            $table->bigInteger('alt_id')->unsigned()->default(1);
            $table->string('slug')->unique();
            $table->string('name')->unique();
            $table->text('desc')->nullable();
            $table->string('border')->nullable();
            $table->integer('max_cats')->unsigned();
            $table->integer('max_models')->unsigned()->nullable();
            $table->integer('max_models_daily')->unsigned()->nullable();
            $table->integer('position')->unsigned();
            $table->integer('visible')->unsigned();
            $table->unsignedInteger('apply_status');
            $table->unsignedInteger('url')->nullable();
            $table->unsignedInteger('backlink')->nullable();

            $table->timestamps();
        });

        // Full Text Index
        DB::statement('ALTER TABLE `groups` ADD FULLTEXT fulltext_index (name)');

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
