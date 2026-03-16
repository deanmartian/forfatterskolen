<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('community_notifications')) {
            Schema::create('community_notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->unsignedInteger('user_id');
                $table->string('type');
                $table->text('content');
                $table->unsignedInteger('from_user_id')->nullable();
                $table->boolean('read')->default(false);
                $table->string('link')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('from_user_id')->references('id')->on('users')->onDelete('set null');
                $table->index('user_id');
            });
        } else {
            Schema::table('community_notifications', function (Blueprint $table) {
                if (!Schema::hasColumn('community_notifications', 'user_id')) {
                    $table->unsignedInteger('user_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('community_notifications', 'type')) {
                    $table->string('type')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('community_notifications', 'content')) {
                    $table->text('content')->nullable()->after('type');
                }
                if (!Schema::hasColumn('community_notifications', 'from_user_id')) {
                    $table->unsignedInteger('from_user_id')->nullable()->after('content');
                }
                if (!Schema::hasColumn('community_notifications', 'read')) {
                    $table->boolean('read')->default(false)->after('from_user_id');
                }
                if (!Schema::hasColumn('community_notifications', 'link')) {
                    $table->string('link')->nullable()->after('read');
                }
                if (!Schema::hasColumn('community_notifications', 'created_at')) {
                    $table->timestamp('created_at')->useCurrent();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('community_notifications');
    }
};
