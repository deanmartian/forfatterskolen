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
        Schema::table('courses_taken', function (Blueprint $table) {
            $table->tinyInteger('can_receive_email')->after('is_welcome_email_sent')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses_taken', function (Blueprint $table) {
            $table->dropColumn('can_receive_email');
        });
    }
};
