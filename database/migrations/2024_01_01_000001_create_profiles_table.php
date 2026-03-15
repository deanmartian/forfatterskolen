<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedInteger('user_id');
            $table->string('name');
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
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
