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
        Schema::create('webinar_registrants', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('webinar_id')->unsigned()->index('webinar_id');
            $table->integer('user_id')->unsigned()->index('user_id');
            $table->string('join_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('webinar_registrants');
    }
};
