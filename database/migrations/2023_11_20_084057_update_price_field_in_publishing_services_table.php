<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePriceFieldInPublishingServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('publishing_services', function (Blueprint $table) {
            $table->decimal('price', 11, 3)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('publishing_services', function (Blueprint $table) {
            $table->decimal('price', 11, 2)->change();
        });
    }
}
