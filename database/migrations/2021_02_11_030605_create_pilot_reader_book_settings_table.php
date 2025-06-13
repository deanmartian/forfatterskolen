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
        Schema::create('pilot_reader_book_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('book_id')->index('book_settings_book_id_foreign');
            $table->boolean('is_reading_reminder_on')->default(0);
            $table->boolean('days_of_reminder')->default(3);
            $table->boolean('will_receive_a_feedback_email')->default(1);
            $table->boolean('is_feedback_shared')->default(0);
            $table->boolean('is_inline_commenting_allowed')->default(0);
            $table->string('book_units', 150)->default('Chapter');
            $table->boolean('is_deactivated')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('pilot_reader_book_settings');
    }
};
