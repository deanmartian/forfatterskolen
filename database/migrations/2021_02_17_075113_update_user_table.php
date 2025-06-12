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
        DB::beginTransaction();

        DB::table('users')
            ->where('is_editor', 1)
            ->update([
                'role' => 3,
            ]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_editor');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('head_editor')->after('minimal_access')->default(0)->nullable();
        });

        DB::table('users')
            ->where('id', 1136)
            ->update([
                'head_editor' => 1,
            ]);

        DB::commit();

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
