<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('course_order_attachments', function (Blueprint $table) {
            $table->foreign('course_id', 'course_order_attachments_course_id')->references('id')->on('courses')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('package_id', 'course_order_attachments_package_id')->references('id')->on('packages')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('user_id', 'course_order_attachments_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_order_attachments', function (Blueprint $table) {
            $table->dropForeign('course_order_attachments_course_id');
            $table->dropForeign('course_order_attachments_package_id');
            $table->dropForeign('course_order_attachments_user_id');
        });
    }
};
