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
        Schema::create('go_to_webinars', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('title');
            $table->string('gt_webinar_key');
            $table->dateTime('webinar_date')->nullable();
            $table->dateTime('reminder_date')->nullable();
            $table->text('confirmation_email')->nullable();
            $table->boolean('send_reminder')->default(0);
            $table->text('reminder_email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('go_to_webinars');
    }
};
