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
        Schema::table('personal_trainer_applicants', function (Blueprint $table) {
            $table->foreign('user_id', 'pt_applicants_user_id_foreign')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_trainer_applicants', function (Blueprint $table) {
            $table->dropForeign('pt_applicants_user_id_foreign');
        });
    }
};
