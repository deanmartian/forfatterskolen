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
    public function up()
    {
        Schema::create('assignment_templates', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('title', 100);
            $table->text('description');
            $table->string('submission_date', 100)->nullable();
            $table->date('available_date')->nullable();
            $table->integer('max_words');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('assignment_templates');
    }
};
