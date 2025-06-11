<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPackageWorkshopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_workshops', function (Blueprint $table) {
            $table->foreign('package_id', 'package_workshops_ibfk_1')->references('id')->on('packages')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('workshop_id', 'package_workshops_ibfk_2')->references('id')->on('workshops')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_workshops', function (Blueprint $table) {
            $table->dropForeign('package_workshops_ibfk_1');
            $table->dropForeign('package_workshops_ibfk_2');
        });
    }
}
