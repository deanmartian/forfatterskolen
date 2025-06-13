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
        Schema::create('free_webinars', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('title');
            $table->text('description');
            $table->dateTime('start_date');
            $table->string('image')->nullable();
            $table->string('gtwebinar_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('free_webinars');
    }
};
