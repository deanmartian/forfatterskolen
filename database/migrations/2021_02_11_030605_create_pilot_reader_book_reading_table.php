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
        Schema::create('pilot_reader_book_reading', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('book_id')->index('book_id');
            $table->integer('user_id')->unsigned()->index('user_id');
            $table->string('role', 100)->default('reader');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('last_seen')->nullable();
            $table->boolean('status')->default(0);
            $table->dateTime('status_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('pilot_reader_book_reading');
    }
};
