<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\User;

class UpdateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::beginTransaction();

        DB::table('users')
            ->where('is_editor',1)
            ->update([
                "role" => 3
        ]);
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_editor');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('head_editor')->after('minimal_access')->default(0)->nullable();
        });

        DB::table('users')
            ->where('id',1136)
            ->update([
                "head_editor" => 1
        ]);

        DB::commit();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
