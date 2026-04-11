<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_staff', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('user_id'); // redaktør/mentor
            $table->string('role', 50); // editor, mentor, guest_editor, course_leader, webinar_host
            $table->unsignedBigInteger('student_user_id')->nullable(); // elev (for editor-tildeling)
            $table->unsignedBigInteger('webinar_id')->nullable(); // for webinar_host
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['course_id', 'role']);
            $table->index(['user_id', 'role']);
            $table->index('student_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_staff');
    }
};
