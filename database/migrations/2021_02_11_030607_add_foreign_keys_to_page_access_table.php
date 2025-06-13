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
        Schema::table('page_access', function (Blueprint $table) {
            $table->foreign('user_id', 'user_id_ibfk1')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_access', function (Blueprint $table) {
            $table->dropForeign('user_id_ibfk1');
        });
    }
};
