<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFileFieldInCorrectionCopyEditingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('copy_editing_manuscripts', function (Blueprint $table) {
            $table->string('file')->nullable()->change();
        });

        Schema::table('correction_manuscripts', function (Blueprint $table) {
            $table->string('file')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('copy_editing_manuscripts', function (Blueprint $table) {
            $table->string('file')->nullable()->change();
        });

        Schema::table('correction_manuscripts', function (Blueprint $table) {
            $table->string('file')->nullable()->change();
        });
    }
}
