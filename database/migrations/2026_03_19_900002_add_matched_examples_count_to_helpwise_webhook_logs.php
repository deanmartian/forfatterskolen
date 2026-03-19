<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('helpwise_webhook_logs', function (Blueprint $table) {
            $table->unsignedInteger('matched_examples_count')->default(0)->after('ai_response');
        });
    }

    public function down(): void
    {
        Schema::table('helpwise_webhook_logs', function (Blueprint $table) {
            $table->dropColumn('matched_examples_count');
        });
    }
};
