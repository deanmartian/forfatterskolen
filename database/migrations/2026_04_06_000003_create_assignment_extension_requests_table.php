<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_extension_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('assignment_id');
            $table->unsignedInteger('user_id');
            $table->date('original_deadline');
            $table->date('requested_deadline');
            $table->text('reason');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->unsignedInteger('decided_by')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_extension_requests');
    }
};
