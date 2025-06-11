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
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('admin_signature')->nullable()->after('signature_label');
            $table->string('admin_name')->nullable()->after('admin_signature');
            $table->string('admin_signed_date')->nullable()->after('admin_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('admin_signature');
            $table->dropColumn('admin_name');
            $table->dropColumn('admin_signed_date');
        });
    }
};
