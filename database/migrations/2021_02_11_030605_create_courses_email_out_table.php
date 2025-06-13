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
        Schema::create('courses_email_out', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('course_id')->unsigned()->index('course_id');
            $table->string('subject');
            $table->text('message');
            $table->string('delay', 50);
            $table->string('from_name')->nullable();
            $table->string('from_email')->nullable();
            $table->string('attachment')->nullable();
            $table->string('attachment_hash')->nullable();
            $table->boolean('for_free_course')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('courses_email_out');
    }
};
