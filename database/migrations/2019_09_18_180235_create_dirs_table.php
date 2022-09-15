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
class CreateDirsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dirs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug')->unique();
            $table->bigInteger('group_id')->unsigned()->index();
            $table->bigInteger('user_id')->unsigned()->nullable()->index();
            $table->string('title');
            $table->longText('content_html');
            $table->longText('content');
            $table->string('notes')->nullable();
            $table->string('url')->unique()->nullable();
            $table->tinyInteger('status')->unsigned();
            $table->timestamp('privileged_at')->nullable();
            $table->timestamp('privileged_to')->nullable();
            $table->timestamps();
        });

        // Full Text Index
        DB::statement("ALTER TABLE dirs ADD FULLTEXT fulltext_index (title, content, url)");

        Schema::create('dirs_backlinks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('dir_id')->unsigned()->index();
            $table->bigInteger('link_id')->unsigned()->index();
            $table->string('url');
            $table->integer('attempts')->unsigned();
            $table->timestamp('attempted_at')->nullable();
            $table->timestamps();

            $table->foreign('dir_id')
                ->references('id')
                ->on('dirs')
                ->onDelete('cascade');

            $table->foreign('link_id')
                ->references('id')
                ->on('links')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dirs');
        Schema::dropIfExists('dirs_backlinks');
    }
}
