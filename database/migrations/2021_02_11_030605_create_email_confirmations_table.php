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
        Schema::create('email_confirmations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('email_confirmations_user_id_foreign');
            $table->string('email', 150);
            $table->string('token', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('email_confirmations');
    }
};
