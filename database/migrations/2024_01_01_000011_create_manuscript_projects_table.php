<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('manuscript_projects')) {
            Schema::create('manuscript_projects', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->unsignedInteger('user_id');
                $table->string('title');
                $table->string('genre');
                $table->text('description')->nullable();
                $table->integer('word_count')->default(0);
                $table->string('status')->default('pågår');
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('user_id');
            });
        } else {
            Schema::table('manuscript_projects', function (Blueprint $table) {
                if (!Schema::hasColumn('manuscript_projects', 'user_id')) {
                    $table->unsignedInteger('user_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('manuscript_projects', 'title')) {
                    $table->string('title')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('manuscript_projects', 'genre')) {
                    $table->string('genre')->nullable()->after('title');
                }
                if (!Schema::hasColumn('manuscript_projects', 'description')) {
                    $table->text('description')->nullable()->after('genre');
                }
                if (!Schema::hasColumn('manuscript_projects', 'word_count')) {
                    $table->integer('word_count')->default(0)->after('description');
                }
                if (!Schema::hasColumn('manuscript_projects', 'status')) {
                    $table->string('status')->default('pågår')->after('word_count');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('manuscript_projects');
    }
};
