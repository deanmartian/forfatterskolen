<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHoursWorked extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assignment_feedbacks', function (Blueprint $table) {
            $table->decimal('hours_worked', 11)->after('locked')->default(0)->nullable();
        });
        Schema::table('assignment_feedbacks_no_group', function (Blueprint $table) {
            $table->decimal('hours_worked', 11)->after('locked')->default(0)->nullable();
        });
        Schema::table('shop_manuscript_taken_feedbacks', function (Blueprint $table) {
            $table->decimal('hours_worked', 11)->after('notes')->default(0)->nullable();
        });
        Schema::table('other_service_feedbacks', function (Blueprint $table) {
            $table->decimal('hours_worked', 11)->after('manuscript')->default(0)->nullable();
        });
        Schema::table('coaching_timer_manuscripts', function (Blueprint $table) {
            $table->decimal('hours_worked', 11)->after('document')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assignment_feedbacks', function ($table) {
            $table->dropColumn('hours_worked');
        });
        Schema::table('assignment_feedbacks_no_group', function ($table) {
            $table->dropColumn('hours_worked');
        });
        Schema::table('shop_manuscript_taken_feedbacks', function ($table) {
            $table->dropColumn('hours_worked');
        });
        Schema::table('other_service_feedbacks', function ($table) {
            $table->dropColumn('hours_worked');
        });
        Schema::table('coaching_timer_manuscripts', function ($table) {
            $table->dropColumn('hours_worked');
        });
    }
}
