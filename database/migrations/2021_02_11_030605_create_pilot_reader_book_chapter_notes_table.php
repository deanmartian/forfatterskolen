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
        Schema::create('pilot_reader_book_chapter_notes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('pilot_reader_book_chapter_id')->index('pilot_reader_book_chapter_id');
            $table->string('mark', 150)->default('unmarked');
            $table->boolean('published')->default(0);
            $table->text('message', 65535)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('pilot_reader_book_chapter_notes');
    }
};
