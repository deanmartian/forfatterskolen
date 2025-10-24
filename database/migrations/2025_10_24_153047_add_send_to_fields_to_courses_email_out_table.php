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
            $table->tinyInteger('send_to_learners_no_course')->default(0)->after('send_immediately');
            $table->tinyInteger('send_to_learners_with_unpaid_pay_later')->default(0)->after('send_to_learners_no_course');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses_email_out', function (Blueprint $table) {
            $table->dropColumn('send_to_learners_no_course');
            $table->dropColumn('send_to_learners_with_unpaid_pay_later');
        });
    }
};
