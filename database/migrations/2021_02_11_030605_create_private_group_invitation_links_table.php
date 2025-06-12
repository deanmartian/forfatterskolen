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
        Schema::create('private_group_invitation_links', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('private_group_id')->unsigned()->index('private_group_invitation_links_private_group_id_foreign');
            $table->string('link_token', 50);
            $table->boolean('enabled')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('private_group_invitation_links');
    }
};
