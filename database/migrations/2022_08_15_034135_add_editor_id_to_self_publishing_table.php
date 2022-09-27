<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEditorIdToSelfPublishingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('self_publishing', function (Blueprint $table) {
            $table->unsignedInteger('editor_id')->nullable()->after('word_count');

            $table->foreign('editor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('self_publishing', function (Blueprint $table) {
            $table->dropColumn('editor_id');
        });
    }
}
