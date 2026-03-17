<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('free_webinars', function (Blueprint $table) {
            $table->text('learning_points')->nullable()->after('description');
            $table->text('target_audience')->nullable()->after('learning_points');
            $table->string('replay_url')->nullable()->after('target_audience');
        });
    }

    public function down(): void
    {
        Schema::table('free_webinars', function (Blueprint $table) {
            $table->dropColumn(['learning_points', 'target_audience', 'replay_url']);
        });
    }
};
