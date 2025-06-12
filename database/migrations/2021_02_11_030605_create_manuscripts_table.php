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
        Schema::create('manuscripts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('coursetaken_id')->unsigned()->index('course_id');
            $table->text('filename');
            $table->integer('word_count');
            $table->float('grade', 11, 1)->nullable();
            $table->integer('feedback_user_id')->unsigned()->nullable()->index('feedback_user_id');
            $table->date('expected_finish')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('manuscripts');
    }
};
