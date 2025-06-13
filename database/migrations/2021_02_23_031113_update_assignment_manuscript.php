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
        // Add approved column, set approved value = 1 where has feedback = 1
        Schema::table('assignment_manuscripts', function (Blueprint $table) {
            $table->tinyInteger('status')->after('has_feedback')->default(0)->nullable();
        });

        DB::beginTransaction();
        DB::table('assignment_manuscripts')
            ->where('has_feedback', 1)
            ->update([
                'status' => 1,
            ]);
        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table->dropColumn(['status']);
    }
};
