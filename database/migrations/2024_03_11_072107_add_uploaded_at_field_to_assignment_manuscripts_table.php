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
        Schema::table('assignment_manuscripts', function (Blueprint $table) {
            $table->timestamp('uploaded_at')->after('manuscript_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_manuscripts', function (Blueprint $table) {
            $table->dropColumn('uploaded_at');
        });
    }
};
