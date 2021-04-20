<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AddColumnFulltextIndexToAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `dirs` ADD FULLTEXT `fulltext_url` (`url`)");
        DB::statement("ALTER TABLE `dirs` ADD FULLTEXT `fulltext_title` (`title`)");
        DB::statement("ALTER TABLE `dirs` ADD FULLTEXT `fulltext_content` (`content`)");

        DB::statement("OPTIMIZE TABLE `dirs`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `dirs` DROP INDEX `fulltext_url`");
        DB::statement("ALTER TABLE `dirs` DROP INDEX `fulltext_title`");
        DB::statement("ALTER TABLE `dirs` DROP INDEX `fulltext_content`");
    }
}
