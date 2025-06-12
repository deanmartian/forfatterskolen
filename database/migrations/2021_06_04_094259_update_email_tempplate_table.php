<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('email_template', function (Blueprint $table) {
            $table->unsignedInteger('course_id')->nullable()->after('email_content');
            $table->string('course_type', 100)->nullable()->after('course_id');

            $table->foreign('course_id')->references('id')->on('courses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('email_template', function (Blueprint $table) {
            $table->dropColumn('course_id');
            $table->dropColumn('course_type');
        });
    }
};
