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
        Schema::create('private_group_members', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('private_group_id')->unsigned()->index('private_group_members_private_group_id_foreign');
            $table->integer('user_id')->unsigned()->index('private_group_members_author_id_foreign');
            $table->string('role', 10)->default('members');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('private_group_members');
    }
};
