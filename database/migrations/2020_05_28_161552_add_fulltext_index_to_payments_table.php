<?php

use Illuminate\Database\Migrations\Migration;

class AddFulltextIndexToPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Full Text Index
        DB::statement("ALTER TABLE `payments` ADD FULLTEXT `fulltext_index` (`logs`)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `payments` DROP INDEX `fulltext_index`");
    }
}
