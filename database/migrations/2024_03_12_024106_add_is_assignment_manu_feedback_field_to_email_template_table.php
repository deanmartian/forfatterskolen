<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('email_template', function (Blueprint $table) {
            $table->tinyInteger('is_assignment_manu_feedback')->after('course_type')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_template', function (Blueprint $table) {
            $table->dropColumn('is_assignment_manu_feedback');
        });
    }
};
