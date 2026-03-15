<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('direct_messages')) {
            Schema::create('direct_messages', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->unsignedInteger('sender_id');
                $table->unsignedInteger('recipient_id');
                $table->text('content');
                $table->boolean('read')->default(false);
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('recipient_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('sender_id');
                $table->index('recipient_id');
            });
        } else {
            Schema::table('direct_messages', function (Blueprint $table) {
                if (!Schema::hasColumn('direct_messages', 'sender_id')) {
                    $table->unsignedInteger('sender_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('direct_messages', 'recipient_id')) {
                    $table->unsignedInteger('recipient_id')->nullable()->after('sender_id');
                }
                if (!Schema::hasColumn('direct_messages', 'content')) {
                    $table->text('content')->nullable()->after('recipient_id');
                }
                if (!Schema::hasColumn('direct_messages', 'read')) {
                    $table->boolean('read')->default(false)->after('content');
                }
                if (!Schema::hasColumn('direct_messages', 'created_at')) {
                    $table->timestamp('created_at')->useCurrent();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('direct_messages');
    }
};
