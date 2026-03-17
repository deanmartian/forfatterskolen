<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses_email_out', function (Blueprint $table) {
            $table->string('template_type', 50)->nullable()->after('message');
            $table->json('template_data')->nullable()->after('template_type');
            $table->unsignedBigInteger('lesson_id')->nullable()->after('course_id');
            $table->boolean('auto_generated')->default(false)->after('exclude_free_manuscript_learners');
            $table->string('status', 20)->default('active')->after('auto_generated');
        });
    }

    public function down(): void
    {
        Schema::table('courses_email_out', function (Blueprint $table) {
            $table->dropColumn(['template_type', 'template_data', 'lesson_id', 'auto_generated', 'status']);
        });
    }
};
