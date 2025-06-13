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
        Schema::dropIfExists('editor_assignment_prices');
        Schema::create('editor_assignment_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('assignment', 100)->default('');
            $table->string('unit', 40)->default('');
            $table->decimal('price', 11)->default(0);
            $table->timestamps();
        });

        DB::beginTransaction();
        $data = [
            ['assignment' => 'Shop Manuscript', 'unit' => 'Hour', 'price' => '0.00'],
            ['assignment' => 'Assignment', 'unit' => 'Item', 'price' => '0.00'],
            ['assignment' => 'Coaching Timer', 'unit' => 'Item', 'price' => '0.00'],
            ['assignment' => 'Correction', 'unit' => 'Hour', 'price' => '0.00'],
            ['assignment' => 'Copy Editing', 'unit' => 'Hour', 'price' => '0.00'],
        ];
        DB::table('editor_assignment_prices')->insert($data);
        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('editor_assignment_prices');
    }
};
