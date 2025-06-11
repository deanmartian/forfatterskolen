<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `manuscript_editor_can_takes` MODIFY `note` VARCHAR(1000) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `manuscript_editor_can_takes` MODIFY `note` VARCHAR(1000) NOT NULL');
    }
};
