<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToLessonContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lesson_contents', function (Blueprint $table) {
            $table->longText('description')->nullable()->after('lesson_content');
            $table->date('date')->nullable()->after('tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lesson_contents', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('date');
        });
    }
}
