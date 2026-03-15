<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('manuscript_excerpts')) {
            Schema::create('manuscript_excerpts', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('project_id');
                $table->unsignedInteger('user_id');
                $table->string('title');
                $table->text('content');
                $table->integer('word_count')->default(0);
                $table->timestamps();

                $table->foreign('project_id')->references('id')->on('manuscript_projects')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('project_id');
            });
        } else {
            Schema::table('manuscript_excerpts', function (Blueprint $table) {
                if (!Schema::hasColumn('manuscript_excerpts', 'project_id')) {
                    $table->uuid('project_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('manuscript_excerpts', 'user_id')) {
                    $table->unsignedInteger('user_id')->nullable()->after('project_id');
                }
                if (!Schema::hasColumn('manuscript_excerpts', 'title')) {
                    $table->string('title')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('manuscript_excerpts', 'content')) {
                    $table->text('content')->nullable()->after('title');
                }
                if (!Schema::hasColumn('manuscript_excerpts', 'word_count')) {
                    $table->integer('word_count')->default(0)->after('content');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('manuscript_excerpts');
    }
};
