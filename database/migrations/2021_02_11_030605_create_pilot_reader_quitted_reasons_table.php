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
        Schema::create('pilot_reader_quitted_reasons', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('book_reader_id')->index('book_reader_id');
            $table->text('reasons', 65535)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('pilot_reader_quitted_reasons');
    }
};
