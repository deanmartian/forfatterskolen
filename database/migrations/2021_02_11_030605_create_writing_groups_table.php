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
        Schema::create('writing_groups', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('contact_id')->unsigned()->index('contact_id');
            $table->text('name', 65535);
            $table->text('description');
            $table->string('group_photo')->nullable();
            $table->text('next_meeting', 65535)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('writing_groups');
    }
};
