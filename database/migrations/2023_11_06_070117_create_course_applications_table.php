<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_applications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id')->unsigned()->index('course_application_package_id_foreign');
            $table->integer('user_id')->unsigned()->index('course_application_user_id_foreign');
            $table->integer('age')->nullable();
            $table->text('optional_words')->nullable();
            $table->longText('reason_for_applying');
            $table->longText('need_in_course');
            $table->longText('expectations');
            $table->longText('how_ready');
            $table->string('file_path')->nullable();
            $table->timestamp('approved_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_applications');
    }
};
