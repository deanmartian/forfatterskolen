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
        Schema::table('former_courses', function (Blueprint $table) {
            $table->foreign('package_id', 'former_courses_package')->references('id')->on('packages')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('user_id', 'former_courses_user')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('former_courses', function (Blueprint $table) {
            $table->dropForeign('former_courses_package');
            $table->dropForeign('former_courses_user');
        });
    }
};
