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
        Schema::create('workshop_email_log', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('workshop_id')->unsigned()->index('workshop_id');
            $table->string('subject');
            $table->text('message');
            $table->text('learners')->nullable();
            $table->string('from_name')->nullable();
            $table->string('from_email')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('workshop_email_log');
    }
};
