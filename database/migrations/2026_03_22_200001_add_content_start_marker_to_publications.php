<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->string('content_start_marker', 500)->nullable()->after('colophon_extra');
        });
    }
    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->dropColumn('content_start_marker');
        });
    }
};
