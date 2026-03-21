<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompletedAtToShopManuscriptsTakenTable extends Migration
{
    public function up()
    {
        Schema::table('shop_manuscripts_taken', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('expected_finish');
        });
    }

    public function down()
    {
        Schema::table('shop_manuscripts_taken', function (Blueprint $table) {
            $table->dropColumn('completed_at');
        });
    }
}
