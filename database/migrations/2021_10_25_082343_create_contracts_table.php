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
        Schema::create('contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('title');
            $table->string('image')->nullable();
            $table->longText('details')->nullable();
            $table->string('signature_label')->mullable();
            $table->string('signature')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('receiver_email')->nullable();
            $table->timestamp('signed_date')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('contracts');
    }
};
