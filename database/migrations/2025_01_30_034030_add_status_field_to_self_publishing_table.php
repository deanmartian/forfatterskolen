<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusFieldToSelfPublishingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('self_publishing', function (Blueprint $table) {
            $table->enum('status', ['pending', 'started', 'finished'])->after('expected_finish')->nullable();
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
            $table->dropColumn('status');
        });
    }
}
