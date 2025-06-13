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
        Schema::create('blog', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('user_id');
            $table->string('title');
            $table->text('description');
            $table->string('image');
            $table->string('author_name')->nullable();
            $table->string('author_image')->nullable();
            $table->boolean('status')->default(1);
            $table->date('schedule')->nullable()->comment('Date to be displayed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('blog');
    }
};
