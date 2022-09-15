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

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

// phpcs:ignore
class AddColumnFulltextIndexToDirsTable extends Migration
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
