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
        Schema::table('assignment_addons', function (Blueprint $table) {
            $table->foreign('assignment_id', 'addon_assignment_id_ibfk1')->references('id')->on('assignments')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('user_id', 'addon_user_id_ibfk1')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_addons', function (Blueprint $table) {
            $table->dropForeign('addon_assignment_id_ibfk1');
            $table->dropForeign('addon_user_id_ibfk1');
        });
    }
};
