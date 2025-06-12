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
        Schema::create('lessons_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lesson_id')->unsigned()->index('FK_lessons_documents_lessons');
            $table->string('name');
            $table->string('document');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('lessons_documents');
    }
};
