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
        Schema::create('email_history', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('subject');
            $table->string('from_email');
            $table->text('message');
            $table->string('parent');
            $table->integer('parent_id');
            $table->string('recipient', 100)->nullable();
            $table->string('track_code', 100)->nullable();
            $table->dateTime('date_open')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('email_history');
    }
};
