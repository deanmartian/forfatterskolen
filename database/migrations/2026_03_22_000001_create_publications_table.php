<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');

            $table->string('title', 500);
            $table->string('subtitle', 500)->nullable();
            $table->string('author_name', 255);
            $table->string('isbn', 20)->nullable();
            $table->string('language', 10)->default('nb');
            $table->string('genre', 100)->nullable();
            $table->text('description')->nullable();
            $table->text('dedication')->nullable();
            $table->text('colophon_extra')->nullable();

            // Design
            $table->string('theme', 50)->default('classic');
            $table->string('trim_size', 20)->default('140x220');
            $table->string('paper_type', 50)->default('munken_cream_100');
            $table->string('binding_type', 50)->default('paperback');
            $table->string('cover_lamination', 20)->default('matt');

            // Files
            $table->string('source_manuscript', 1000);
            $table->string('parsed_html', 1000)->nullable();
            $table->string('output_pdf', 1000)->nullable();
            $table->string('output_epub', 1000)->nullable();
            $table->string('output_docx', 1000)->nullable();
            $table->string('cover_front', 1000)->nullable();
            $table->string('cover_back', 1000)->nullable();
            $table->string('cover_spine', 1000)->nullable();

            // Metadata
            $table->integer('word_count')->nullable();
            $table->integer('page_count')->nullable();
            $table->integer('chapter_count')->nullable();
            $table->float('spine_width_mm')->nullable();

            // Status
            $table->string('status', 20)->default('draft');
            $table->text('error_message')->nullable();
            $table->tinyInteger('wizard_step')->default(1);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};
