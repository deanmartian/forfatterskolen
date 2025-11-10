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
        Schema::table('courses_email_out', function (Blueprint $table) {
            $table->tinyInteger('include_former_learners')->default(0)->after('send_to_learners_with_unpaid_pay_later');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses_email_out', function (Blueprint $table) {
            $table->dropColumn('include_former_learners');
        });
    }
};
