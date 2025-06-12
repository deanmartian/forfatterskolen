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
        Schema::create('private_group_member_invitations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email', 150);
            $table->integer('private_group_id')->unsigned()->index('private_group_member_invitations_private_group_id_foreign');
            $table->string('token', 50);
            $table->boolean('status')->default(0);
            $table->boolean('send_count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('private_group_member_invitations');
    }
};
