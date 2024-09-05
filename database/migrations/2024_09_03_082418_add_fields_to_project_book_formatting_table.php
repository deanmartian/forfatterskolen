<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToProjectBookFormattingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_book_formatting', function (Blueprint $table) {
            $table->unsignedInteger('designer_id')->nullable()->after('file');
            $table->string('feedback')->after('designer_id')->nullable();
            $table->enum('feedback_status', ['pending', 'completed'])->nullable()->after('feedback');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_book_formatting', function (Blueprint $table) {
            $table->dropColumn('designer_id');
            $table->dropColumn('feedback');
            $table->dropColumn('feedback_status');
        });
    }
}
