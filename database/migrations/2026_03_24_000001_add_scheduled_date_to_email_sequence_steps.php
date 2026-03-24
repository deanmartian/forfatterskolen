<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('email_sequence_steps', 'scheduled_date')) {
            Schema::table('email_sequence_steps', function (Blueprint $table) {
                $table->date('scheduled_date')->nullable()->after('send_time');
            });
        }
    }

    public function down(): void
    {
        Schema::table('email_sequence_steps', function (Blueprint $table) {
            $table->dropColumn('scheduled_date');
        });
    }
};
