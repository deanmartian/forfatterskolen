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
        Schema::create('assignment_manuscripts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('assignment_id')->unsigned()->index('assignment_id');
            $table->integer('user_id')->unsigned()->index('user_id');
            $table->string('filename')->default('');
            $table->integer('words')->default(0);
            $table->decimal('grade', 11)->nullable();
            $table->integer('type')->nullable()->default(0);
            $table->integer('manu_type')->nullable()->default(0);
            $table->boolean('locked')->nullable()->default(0);
            $table->integer('text_number')->nullable()->default(0);
            $table->integer('editor_id')->default(0);
            $table->boolean('has_feedback')->default(0);
            $table->boolean('join_group')->default(0);
            $table->date('expected_finish')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('assignment_manuscripts');
    }
};
