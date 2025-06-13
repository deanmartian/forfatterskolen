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
        Schema::create('publishing_services', function (Blueprint $table) {
            $table->increments('id');
            $table->string('product_service', 500);
            $table->longText('description')->nullable();
            $table->decimal('price', 11, 2)->nullable();
            $table->decimal('per_word_hour', 11, 2)->nullable();
            $table->string('per_unit', 50)->nullable();
            $table->decimal('base_char_word', 11, 2)->default(0);
            $table->string('slug', 1000);
            $table->string('service_type')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publishing_services');
    }
};
