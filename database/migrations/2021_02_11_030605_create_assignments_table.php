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
        Schema::create('assignments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100);
            $table->integer('course_id')->unsigned()->nullable()->index('course_id');
            $table->text('description');
            $table->string('submission_date', 100)->nullable();
            $table->date('available_date')->nullable();
            $table->string('allowed_package')->nullable();
            $table->decimal('add_on_price', 11)->nullable()->default(0.00);
            $table->integer('max_words');
            $table->integer('for_editor');
            $table->string('editor_manu_generate_count', 10)->nullable();
            $table->string('generated_filepath')->nullable();
            $table->boolean('show_join_group_question')->default(1);
            $table->integer('parent_id')->nullable();
            $table->string('parent', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('assignments');
    }
};
