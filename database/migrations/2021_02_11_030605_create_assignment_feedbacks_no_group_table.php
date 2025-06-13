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
        Schema::create('assignment_feedbacks_no_group', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('assignment_manuscript_id')->unsigned()->index('assignment_manuscript_id');
            $table->integer('learner_id')->unsigned()->index('learner_id');
            $table->integer('feedback_user_id')->unsigned()->index('feedback_user_id');
            $table->text('filename', 65535);
            $table->boolean('is_admin')->default(0);
            $table->integer('is_active')->default(0);
            $table->date('availability')->nullable();
            $table->boolean('locked')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('assignment_feedbacks_no_group');
    }
};
