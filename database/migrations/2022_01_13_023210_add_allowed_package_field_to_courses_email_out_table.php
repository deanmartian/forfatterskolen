<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('courses_email_out', function (Blueprint $table) {
            $table->string('allowed_package')->nullable()->after('from_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('courses_email_out', function (Blueprint $table) {
            $table->dropColumn('allowed_package');
        });
    }
};
