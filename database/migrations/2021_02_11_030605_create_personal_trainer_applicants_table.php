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
        Schema::create('personal_trainer_applicants', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->unsigned()->index('pt_applicants_user_id_foreign');
            $table->integer('age')->nullable();
            $table->text('optional_words', 65535)->nullable();
            $table->text('reason_for_applying', 65535);
            $table->text('need_in_course', 65535);
            $table->text('expectations', 65535);
            $table->text('how_ready', 65535);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('personal_trainer_applicants');
    }
};
