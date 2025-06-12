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
        Schema::create('calendar_note', function (Blueprint $table) {
            $table->integer('id', true);
            $table->text('note', 65535);
            $table->date('from_date');
            $table->date('to_date');
            $table->integer('course_id')->unsigned()->index('course_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('calendar_note');
    }
};
