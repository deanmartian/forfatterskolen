<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('assignment_groups', function (Blueprint $table) {
            $table->foreign('assignment_id', 'assignment_groups_ibfk_1')->references('id')->on('assignments')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('assignment_groups', function (Blueprint $table) {
            $table->dropForeign('assignment_groups_ibfk_1');
        });
    }
};
