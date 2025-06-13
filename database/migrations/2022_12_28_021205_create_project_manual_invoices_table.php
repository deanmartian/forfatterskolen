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
        Schema::create('project_manual_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('project_id')->unsigned();
            $table->string('invoice');
            $table->integer('amount')->nullable();
            $table->integer('assigned_to')->nullable();
            $table->date('date')->nullable();
            $table->longText('note')->nullable();
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_manual_invoices');
    }
};
