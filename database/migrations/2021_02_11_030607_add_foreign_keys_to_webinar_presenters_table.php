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
        Schema::table('webinar_presenters', function (Blueprint $table) {
            $table->foreign('webinar_id', 'webinar_presenters_ibfk_1')->references('id')->on('webinars')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('webinar_presenters', function (Blueprint $table) {
            $table->dropForeign('webinar_presenters_ibfk_1');
        });
    }
};
