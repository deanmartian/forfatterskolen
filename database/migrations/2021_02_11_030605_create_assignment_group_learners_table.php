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
        Schema::create('assignment_group_learners', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('assignment_group_id')->unsigned()->index('assignment_group_id');
            $table->integer('user_id')->unsigned()->index('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('assignment_group_learners');
    }
};
