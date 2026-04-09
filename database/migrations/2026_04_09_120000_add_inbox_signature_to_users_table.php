<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a per-user inbox signature so each admin/editor can configure
     * their own farewell + name + footer for inbox replies. Falls back
     * to the hardcoded default ("Ha en fin dag! / Mvh {name} / Forfatter-
     * skolen / Easywrite / Indiemoon Publishing") when null.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('inbox_signature')->nullable()->after('email_verification_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('inbox_signature');
        });
    }
};
