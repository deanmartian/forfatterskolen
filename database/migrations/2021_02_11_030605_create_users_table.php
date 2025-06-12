<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique('canvas_users_email_unique');
            $table->string('profile_image')->nullable();
            $table->integer('role')->default(2);
            $table->string('gender', 140)->nullable();
            $table->string('birthday', 140)->nullable();
            $table->string('password', 60)->default('y$rXr6fHn5HKmEIJhbAOvNZO7B1V2QC4yMBtc4qEfx751wylM/P524q');
            $table->string('remember_token', 100)->nullable();
            $table->text('notes', 65535)->nullable();
            $table->boolean('minimal_access')->nullable()->default(0);
            $table->boolean('is_editor')->default(0);
            $table->boolean('auto_renew_courses')->default(0);
            $table->boolean('need_pass_update')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('users');
    }
};
