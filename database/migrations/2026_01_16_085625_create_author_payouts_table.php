<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('author_payouts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->integer('year');
            $table->tinyInteger('quarter');
            $table->decimal('amount_total', 10, 2)->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->unsignedInteger('paid_by_user_id')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'year', 'quarter'], 'author_payouts_user_year_quarter_unique');
            $table->index(['year', 'quarter'], 'author_payouts_year_quarter_index');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('paid_by_user_id')
                ->references('id')->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('author_payouts');
    }
};
