<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('manuscript_followers')) {
            Schema::create('manuscript_followers', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('project_id');
                $table->unsignedInteger('user_id');
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('project_id')->references('id')->on('manuscript_projects')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['project_id', 'user_id']);
                $table->index('project_id');
            });
        } else {
            Schema::table('manuscript_followers', function (Blueprint $table) {
                if (!Schema::hasColumn('manuscript_followers', 'project_id')) {
                    $table->uuid('project_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('manuscript_followers', 'user_id')) {
                    $table->unsignedInteger('user_id')->nullable()->after('project_id');
                }
                if (!Schema::hasColumn('manuscript_followers', 'created_at')) {
                    $table->timestamp('created_at')->useCurrent();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('manuscript_followers');
    }
};
