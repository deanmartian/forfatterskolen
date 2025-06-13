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
        Schema::table('lessons_documents', function (Blueprint $table) {
            $table->foreign('lesson_id', 'FK_lessons_documents_lessons')->references('id')->on('lessons')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons_documents', function (Blueprint $table) {
            $table->dropForeign('FK_lessons_documents_lessons');
        });
    }
};
