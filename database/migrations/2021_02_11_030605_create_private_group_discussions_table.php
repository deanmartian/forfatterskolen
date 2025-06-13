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
        Schema::create('private_group_discussions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('private_group_id')->unsigned()->index('private_group_discussions_private_group_id_foreign');
            $table->integer('user_id')->unsigned()->index('private_group_discussions_author_id_foreign');
            $table->string('subject', 150);
            $table->string('message', 150)->nullable();
            $table->boolean('is_announcement')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('private_group_discussions');
    }
};
