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
        Schema::create('private_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150)->unique();
            $table->boolean('policy')->default(1);
            $table->text('welcome_msg', 65535)->nullable();
            $table->string('contact_email', 150)->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('private_groups');
    }
};
