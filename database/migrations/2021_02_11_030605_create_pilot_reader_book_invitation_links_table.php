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
        Schema::create('pilot_reader_book_invitation_links', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('book_id')->index('invitation_links_book_id_foreign');
            $table->string('link_token', 50);
            $table->boolean('enabled')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('pilot_reader_book_invitation_links');
    }
};
