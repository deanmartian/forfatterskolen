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
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('user_id');
            $table->string('fiken_url')->default('');
            $table->string('pdf_url');
            $table->boolean('fiken_is_paid')->default(0);
            $table->decimal('fiken_balance', 11)->default(0.00);
            $table->date('fiken_dueDate')->nullable();
            $table->string('kid_number', 100)->nullable();
            $table->integer('invoice_number')->nullable();
            $table->string('fiken_invoice_id', 50)->nullable();
            $table->date('fiken_issueDate')->nullable();
            $table->bigInteger('gross')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('invoices');
    }
};
