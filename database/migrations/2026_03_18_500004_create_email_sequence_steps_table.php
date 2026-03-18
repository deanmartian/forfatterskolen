<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_sequence_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sequence_id');
            $table->integer('step_number');
            $table->string('subject');
            $table->longText('body_html');
            $table->integer('delay_hours')->default(0); // 0 = straks
            $table->time('send_time')->nullable(); // Foretrukket tidspunkt (f.eks. 10:00)
            $table->string('from_type')->default('transactional'); // transactional, newsletter
            $table->boolean('only_without_active_course')->default(false); // Kun for salgsmail
            $table->timestamps();

            $table->unique(['sequence_id', 'step_number']);

            $table->foreign('sequence_id')->references('id')->on('email_sequences')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_sequence_steps');
    }
};
