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
        Schema::table('contract_templates', function (Blueprint $table) {
            $table->tinyInteger('show_in_project')->default(0)->after('signature_label');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract_templates', function (Blueprint $table) {
            $table->dropColumn('show_in_project');
        });
    }
};
