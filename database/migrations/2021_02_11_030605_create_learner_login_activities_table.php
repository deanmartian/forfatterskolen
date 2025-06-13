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
        Schema::create('learner_login_activities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('learner_login_id')->unsigned();
            $table->string('activity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('learner_login_activities');
    }
};
