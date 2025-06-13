<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->unsignedInteger('project_id')->nullable()->after('id');
            $table->string('sent_file')->nullable()->after('receiver_email');
            $table->string('signed_file')->nullable()->after('sent_file');
            $table->tinyInteger('is_file')->nullable()->default(0)->after('signed_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('project_id');
            $table->dropColumn('sent_file');
            $table->dropColumn('signed_file');
            $table->dropColumn('is_file');
        });
    }
};
