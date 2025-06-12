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
        Schema::create('workshops', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->default('');
            $table->text('description');
            $table->decimal('price', 11);
            $table->string('image')->nullable();
            $table->dateTime('date');
            $table->date('faktura_date')->nullable();
            $table->integer('duration');
            $table->integer('seats');
            $table->string('location')->default('');
            $table->string('gmap')->nullable();
            $table->boolean('is_active')->default(0);
            $table->boolean('is_free')->default(0);
            $table->string('fiken_product', 100)->default('');
            $table->string('email_title')->nullable()->default('');
            $table->text('email_body')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('workshops');
    }
};
