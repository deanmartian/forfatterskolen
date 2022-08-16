<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpectedFinishFieldToSelfPublishingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('self_publishing', function (Blueprint $table) {
            $table->date('expected_finish')->after('editor_share')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('self_publishing', function (Blueprint $table) {
            $table->dropColumn('expected_finish');
        });
    }
}
