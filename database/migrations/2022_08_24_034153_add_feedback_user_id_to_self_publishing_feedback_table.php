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
    public function up()
    {
        Schema::table('self_publishing_feedback', function (Blueprint $table) {
            $table->unsignedInteger('feedback_user_id')->nullable()->after('self_publishing_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('self_publishing_feedback', function (Blueprint $table) {
            $table->dropColumn('feedback_user_id');
        });
    }
};
