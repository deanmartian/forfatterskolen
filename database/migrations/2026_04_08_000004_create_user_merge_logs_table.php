<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Audit-logg for sammenslåtte brukere. Holder et permanent spor av
 * hvem som ble merget hvor og når, slik at vi kan rulle tilbake
 * eller granske hvis noe skulle vise seg å være feil.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_merge_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('primary_user_id');
            $table->unsignedBigInteger('secondary_user_id');
            $table->string('primary_email');
            $table->string('secondary_email');
            $table->json('rows_moved')->nullable(); // {table.column: count}
            $table->json('errors')->nullable();
            $table->unsignedBigInteger('merged_by_user_id')->nullable();
            $table->timestamp('merged_at')->useCurrent();
            $table->timestamps();

            $table->index('primary_user_id');
            $table->index('secondary_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_merge_logs');
    }
};
