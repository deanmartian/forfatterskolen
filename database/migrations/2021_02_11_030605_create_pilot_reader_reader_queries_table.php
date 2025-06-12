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
        Schema::create('pilot_reader_reader_queries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('from')->unsigned()->index('reader_queries_from_foreign');
            $table->integer('to')->unsigned()->index('reader_queries_to_foreign');
            $table->integer('book_id')->unsigned()->index('reader_queries_book_id_foreign');
            $table->text('letter', 65535)->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('pilot_reader_reader_queries');
    }
};
