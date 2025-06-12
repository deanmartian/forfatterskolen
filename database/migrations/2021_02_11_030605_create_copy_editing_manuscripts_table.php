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
        Schema::create('copy_editing_manuscripts', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->unsigned()->index('user_id');
            $table->string('file');
            $table->decimal('payment_price', 11);
            $table->integer('editor_id')->unsigned()->nullable()->index('copy_editing_manuscripts_editor');
            $table->boolean('status')->default(0);
            $table->dateTime('expected_finish')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('copy_editing_manuscripts');
    }
};
