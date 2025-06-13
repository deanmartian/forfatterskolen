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
        Schema::create('workshop_menus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('workshop_id')->unsigned()->index('workshop_id');
            $table->string('title', 100)->default('');
            $table->text('description');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('workshop_menus');
    }
};
