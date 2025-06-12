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
        Schema::create('project_book_sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('project_book_id');
            $table->string('sale_type');
            $table->integer('quantity');
            $table->decimal('amount');
            $table->date('date');
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
        Schema::dropIfExists('project_book_sales');
    }
};
