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
        Schema::table('shop_manuscript_comments', function (Blueprint $table) {
            $table->foreign('shop_manuscript_taken_id', 'shop_manuscript_comments_ibfk_1')->references('id')->on('shop_manuscripts_taken')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('user_id', 'shop_manuscript_comments_ibfk_2')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_manuscript_comments', function (Blueprint $table) {
            $table->dropForeign('shop_manuscript_comments_ibfk_1');
            $table->dropForeign('shop_manuscript_comments_ibfk_2');
        });
    }
};
