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
        Schema::create('webinar_presenters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('webinar_id')->unsigned()->index('workshop_id');
            $table->string('first_name', 100)->default('');
            $table->string('last_name', 100)->default('');
            $table->string('email', 100)->default('');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('webinar_presenters');
    }
};
