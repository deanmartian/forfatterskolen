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
        Schema::create('pilot_reader_reader_query_decisions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('query_id')->unsigned()->index('reader_query_decisions_query_id_foreign');
            $table->text('decision', 65535)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('pilot_reader_reader_query_decisions');
    }
};
