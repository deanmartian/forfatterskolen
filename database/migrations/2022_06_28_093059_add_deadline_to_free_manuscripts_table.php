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
        Schema::table('free_manuscripts', function (Blueprint $table) {
            $table->timestamp('deadline')->nullable()->after('feedback_content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('free_manuscripts', function (Blueprint $table) {
            $table->removeColumn('deadline');
        });
    }
};
