<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('profiles')) {
            Schema::create('profiles', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->unsignedInteger('user_id');
                $table->string('name')->nullable();
                $table->string('author_name')->nullable();
                $table->string('avatar_url')->nullable();
                $table->text('bio')->nullable();
                $table->json('genres')->nullable();
                $table->json('writing_interests')->nullable();
                $table->text('current_project')->nullable();
                $table->enum('badge', ['aktiv_elev', 'tidligere_elev', 'mentor', 'moderator', 'admin'])->default('aktiv_elev');
                $table->enum('access_level', ['community_member', 'course_member', 'premium'])->default('community_member');
                $table->boolean('is_suspended')->default(false);
                $table->timestamps();

                $table->unique('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        } else {
            Schema::table('profiles', function (Blueprint $table) {
                if (!Schema::hasColumn('profiles', 'name')) {
                    $table->string('name')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('profiles', 'author_name')) {
                    $table->string('author_name')->nullable()->after('name');
                }
                if (!Schema::hasColumn('profiles', 'avatar_url')) {
                    $table->string('avatar_url')->nullable()->after('author_name');
                }
                if (!Schema::hasColumn('profiles', 'bio')) {
                    $table->text('bio')->nullable()->after('avatar_url');
                }
                if (!Schema::hasColumn('profiles', 'genres')) {
                    $table->json('genres')->nullable()->after('bio');
                }
                if (!Schema::hasColumn('profiles', 'writing_interests')) {
                    $table->json('writing_interests')->nullable()->after('genres');
                }
                if (!Schema::hasColumn('profiles', 'current_project')) {
                    $table->text('current_project')->nullable()->after('writing_interests');
                }
                if (!Schema::hasColumn('profiles', 'badge')) {
                    $table->enum('badge', ['aktiv_elev', 'tidligere_elev', 'mentor', 'moderator', 'admin'])->default('aktiv_elev')->after('current_project');
                }
                if (!Schema::hasColumn('profiles', 'access_level')) {
                    $table->enum('access_level', ['community_member', 'course_member', 'premium'])->default('community_member')->after('badge');
                }
                if (!Schema::hasColumn('profiles', 'is_suspended')) {
                    $table->boolean('is_suspended')->default(false)->after('access_level');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
