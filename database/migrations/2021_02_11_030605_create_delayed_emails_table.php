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
        Schema::create('delayed_emails', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('subject');
            $table->text('message');
            $table->string('from_name', 100)->nullable();
            $table->string('from_email', 100);
            $table->string('recipient');
            $table->string('attachment')->nullable();
            $table->date('send_date');
            $table->string('parent', 100);
            $table->string('parent_id', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('delayed_emails');
    }
};
