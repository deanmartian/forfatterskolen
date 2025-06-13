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
        Schema::create('coaching_timer_manuscripts', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->unsigned()->index('user_id');
            $table->string('file')->nullable();
            $table->decimal('payment_price', 11)->default(0.00);
            $table->boolean('plan_type');
            $table->text('help_with', 65535)->nullable();
            $table->string('suggested_date')->nullable();
            $table->string('suggested_date_admin')->nullable();
            $table->dateTime('approved_date')->nullable();
            $table->integer('editor_id')->unsigned()->nullable()->index('coaching_timer_manuscripts_editor');
            $table->string('replay_link')->nullable();
            $table->text('comment', 65535)->nullable();
            $table->string('document')->nullable();
            $table->boolean('status')->default(0);
            $table->boolean('is_approved')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('coaching_timer_manuscripts');
    }
};
