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
        Schema::table('writing_groups', function (Blueprint $table) {
            $table->foreign('contact_id', 'contact_id_ibfk_1')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('writing_groups', function (Blueprint $table) {
            $table->dropForeign('contact_id_ibfk_1');
        });
    }
};
