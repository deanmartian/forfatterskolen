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
        Schema::create('assignment_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('assignment_id')->unsigned()->index('assignment_id');
            $table->string('title')->default('');
            $table->dateTime('submission_date')->nullable();
            $table->boolean('allow_feedback_download')->default(0);
            $table->date('availability')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('assignment_groups');
    }
};
