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
        Schema::create('publishing', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('publishing');
            $table->string('home_link')->nullable();
            $table->string('mail_address');
            $table->string('phone', 50);
            $table->string('genre');
            $table->string('send_manuscript_link')->nullable();
            $table->string('email', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('publishing');
    }
};
