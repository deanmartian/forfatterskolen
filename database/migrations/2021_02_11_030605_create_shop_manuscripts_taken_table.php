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
        Schema::create('shop_manuscripts_taken', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('user_id');
            $table->integer('shop_manuscript_id')->unsigned()->index('package_id');
            $table->integer('package_shop_manuscripts_id')->default(0);
            $table->text('file')->nullable();
            $table->integer('words')->nullable();
            $table->float('grade', 11, 1)->nullable();
            $table->boolean('is_active')->default(0);
            $table->integer('feedback_user_id')->unsigned()->nullable()->index('feedback_user_id');
            $table->date('expected_finish')->nullable();
            $table->dateTime('manuscript_uploaded_date')->nullable();
            $table->integer('genre')->default(0);
            $table->text('description', 65535)->nullable();
            $table->boolean('is_manuscript_locked')->default(0);
            $table->text('synopsis')->nullable();
            $table->boolean('coaching_time_later')->nullable()->default(0);
            $table->boolean('is_welcome_email_sent')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('shop_manuscripts_taken');
    }
};
