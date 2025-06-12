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
        Schema::create('courses_taken', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('user_id');
            $table->integer('package_id')->unsigned()->index('package_id');
            $table->boolean('is_active')->default(1);
            $table->dateTime('started_at')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('access_lessons')->default('[]');
            $table->integer('years')->default(1);
            $table->integer('sent_renew_email')->default(0);
            $table->boolean('is_free')->nullable()->default(0);
            $table->boolean('send_expiry_reminder')->nullable()->default(1);
            $table->boolean('is_welcome_email_sent')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('courses_taken');
    }
};
