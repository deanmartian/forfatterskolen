<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_known_issues', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Kort tittel, f.eks. "Passord-reset siden grå på Mac"
            $table->text('description'); // Hva problemet er
            $table->text('workaround')->nullable(); // Hva AI bør foreslå
            $table->enum('status', ['active', 'resolved'])->default('active');
            $table->enum('severity', ['info', 'low', 'medium', 'high'])->default('medium');
            $table->string('category')->nullable(); // f.eks. "innlogging", "betaling", "kurs"
            $table->date('discovered_at')->nullable();
            $table->date('resolved_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['status', 'severity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_known_issues');
    }
};
