<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('course_groups')) {
            Schema::create('course_groups', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('icon')->default('📚');
                $table->timestamps();
            });
        } else {
            Schema::table('course_groups', function (Blueprint $table) {
                if (!Schema::hasColumn('course_groups', 'name')) {
                    $table->string('name')->nullable()->after('id');
                }
                if (!Schema::hasColumn('course_groups', 'description')) {
                    $table->text('description')->nullable()->after('name');
                }
                if (!Schema::hasColumn('course_groups', 'icon')) {
                    $table->string('icon')->default('📚')->after('description');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('course_groups');
    }
};
