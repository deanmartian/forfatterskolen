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
        Schema::create('self_publishing_feedback', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('self_publishing_id');
            $table->longText('manuscript');
            $table->longText('notes')->nullable();
            $table->tinyInteger('is_approved')->default(0);
            $table->timestamps();

            $table->foreign('self_publishing_id')->references('id')->on('self_publishing')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('self_publishing_feedback');
    }
};
