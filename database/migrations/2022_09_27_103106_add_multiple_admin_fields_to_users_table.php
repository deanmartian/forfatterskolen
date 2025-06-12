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
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('is_ghost_writer_admin')->nullable()->default(0)->after('with_head_editor_access');
            $table->tinyInteger('is_copy_editing_admin')->nullable()->default(0)->after('is_ghost_writer_admin');
            $table->tinyInteger('is_correction_admin')->nullable()->default(0)->after('is_copy_editing_admin');
            $table->tinyInteger('is_coaching_admin')->nullable()->default(0)->after('is_correction_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_ghost_writer_admin');
            $table->dropColumn('is_copy_editing_admin');
            $table->dropColumn('is_correction_admin');
            $table->dropColumn('is_coaching_admin');
        });
    }
};
