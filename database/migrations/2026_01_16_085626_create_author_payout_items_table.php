<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('author_payout_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('author_payout_id');
            $table->unsignedBigInteger('project_registration_id');
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['author_payout_id', 'project_registration_id'], 'author_payout_items_unique');
            $table->index('project_registration_id', 'author_payout_items_project_registration_index');

            $table->foreign('author_payout_id')
                ->references('id')->on('author_payouts')
                ->onDelete('cascade');

            $table->foreign('project_registration_id')
                ->references('id')->on('project_registrations')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('author_payout_items');
    }
};
