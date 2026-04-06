<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request_to_editors', function (Blueprint $table) {
            $table->unsignedInteger('project_item_id')->nullable()->after('manuscript_id');
            $table->string('project_item_type')->nullable()->after('project_item_id');
        });
    }

    public function down(): void
    {
        Schema::table('request_to_editors', function (Blueprint $table) {
            $table->dropColumn(['project_item_id', 'project_item_type']);
        });
    }
};
