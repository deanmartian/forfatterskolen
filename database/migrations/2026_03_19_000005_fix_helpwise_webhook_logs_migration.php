<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The 900002 migration tries to add a column to helpwise_webhook_logs
        // but it may not have the ai_response column. Add it safely.
        if (Schema::hasTable('helpwise_webhook_logs')) {
            Schema::table('helpwise_webhook_logs', function (Blueprint $table) {
                if (!Schema::hasColumn('helpwise_webhook_logs', 'matched_examples_count')) {
                    $table->unsignedInteger('matched_examples_count')->default(0)->after('error_message');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('helpwise_webhook_logs') && Schema::hasColumn('helpwise_webhook_logs', 'matched_examples_count')) {
            Schema::table('helpwise_webhook_logs', function (Blueprint $table) {
                $table->dropColumn('matched_examples_count');
            });
        }
    }
};
