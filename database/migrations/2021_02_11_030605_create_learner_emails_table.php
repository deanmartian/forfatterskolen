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
        Schema::create('learner_emails', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('FK_learner_emails_users');
            $table->string('subject');
            $table->text('email', 65535);
            $table->text('attachment', 65535)->nullable();
            $table->string('from_name')->nullable();
            $table->string('from_email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('learner_emails');
    }
};
