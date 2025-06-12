<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses_taken', function (Blueprint $table) {
            $table->foreign('user_id', 'courses_taken_ibfk_1')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('package_id', 'courses_taken_ibfk_2')->references('id')->on('packages')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses_taken', function (Blueprint $table) {
            $table->dropForeign('courses_taken_ibfk_1');
            $table->dropForeign('courses_taken_ibfk_2');
        });
    }
};
