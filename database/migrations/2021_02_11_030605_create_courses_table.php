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
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100)->default('');
            $table->text('description');
            $table->string('course_image')->default('');
            $table->string('type', 50)->default('');
            $table->text('email')->nullable();
            $table->text('course_plan')->nullable();
            $table->text('course_plan_data')->nullable();
            $table->string('instructor')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('extend_courses')->default(0);
            $table->integer('display_order')->default(0);
            $table->boolean('for_sale')->default(1);
            $table->integer('status')->default(1);
            $table->boolean('is_free')->default(0);
            $table->integer('auto_list_id')->default(0);
            $table->string('photographer')->nullable();
            $table->boolean('hide_price')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('courses');
    }
};
