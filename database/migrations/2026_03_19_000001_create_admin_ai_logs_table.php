<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_ai_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_user_id');
            $table->text('prompt');
            $table->string('intent')->nullable();
            $table->decimal('confidence', 3, 2)->nullable();
            $table->boolean('executed')->default(false);
            $table->text('result_summary')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_ai_logs');
    }
};
