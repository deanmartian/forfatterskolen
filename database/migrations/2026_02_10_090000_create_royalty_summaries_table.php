<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('royalty_summaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->integer('year');
            $table->tinyInteger('quarter');
            $table->unsignedInteger('project_registration_id')->nullable();
            $table->decimal('sales_amount', 12, 2)->default(0);
            $table->decimal('cost_amount_base', 12, 2)->default(0);
            $table->decimal('cost_amount_multiplied', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->default(0);
            $table->timestamp('computed_at');
            $table->timestamps();

            $table->index(['user_id', 'year', 'quarter'], 'royalty_summaries_user_year_quarter_index');
            $table->index(['project_registration_id', 'year', 'quarter'], 'royalty_summaries_registration_year_quarter_index');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('royalty_summaries');
    }
};
