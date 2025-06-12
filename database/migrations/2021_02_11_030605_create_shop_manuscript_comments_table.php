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
        Schema::create('shop_manuscript_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shop_manuscript_taken_id')->unsigned()->index('shop_manuscript_taken_id');
            $table->integer('user_id')->unsigned()->index('user_id');
            $table->text('comment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('shop_manuscript_comments');
    }
};
