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
        Schema::table('copy_editing_manuscripts', function (Blueprint $table) {
            $table->tinyInteger('is_locked')->default(0)->after('expected_finish');
        });

        Schema::table('correction_manuscripts', function (Blueprint $table) {
            $table->tinyInteger('is_locked')->default(0)->after('expected_finish');
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
            $table->dropColumn('is_locked');
        });

        Schema::table('correction_manuscripts', function (Blueprint $table) {
            $table->dropColumn('is_locked');
        });
    }
};
