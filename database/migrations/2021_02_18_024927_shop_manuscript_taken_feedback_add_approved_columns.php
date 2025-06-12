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
        Schema::table('shop_manuscript_taken_feedbacks', function (Blueprint $table) {
            $table->tinyInteger('approved')->after('notes')->default(0)->nullable();
            $table->integer('approved_by')->after('approved')->unsigned()->index('approved_by');
            $table->dateTime('approved_at')->after('approved_by')->nullable();
        });

        // set as approved old data
        DB::beginTransaction();
        DB::table('shop_manuscript_taken_feedbacks')
            ->update([
                'approved' => 1,
            ]);
        DB::commit();

        // Schema::table('shop_manuscript_taken_feedbacks', function (Blueprint $table) {
        //     $table->foreign('approved_by', 'approved_by_editor_id')->references('id')->on('users')->onDelete('cascade');
        // });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_manuscript_taken_feedbacks', function ($table) {
            $table->dropColumn(['approved', 'approved_by', 'approved_at']);
            // $table->dropForeign('approved_by_editor_id');
        });
    }
};
