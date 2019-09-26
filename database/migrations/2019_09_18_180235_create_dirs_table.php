<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * [CreateDirsTable description]
 */
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
            $table->unsignedBigInteger('group_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('title');
            $table->longText('content_html');
            $table->longText('content');
            $table->string('notes')->nullable();
            $table->string('url')->unique()->nullable();
            $table->unsignedInteger('status');
            $table->timestamp('privileged_at')->nullable();
            $table->timestamp('privileged_to')->nullable();
            $table->timestamps();
        });

        // Full Text Index
        DB::statement("ALTER TABLE dirs ADD FULLTEXT fulltext_index (title, content, url)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dirs');
    }
}
