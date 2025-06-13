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
        Schema::table('competition_applicants', function (Blueprint $table) {
            $table->foreign('user_id', 'competition_applicant_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_applicants', function (Blueprint $table) {
            $table->dropForeign('competition_applicant_user_id');
        });
    }
};
