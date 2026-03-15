<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('conversations')) {
            Schema::create('conversations', function (Blueprint $table) {
                $table->increments('id');
                $table->string('subject');
                $table->integer('created_by')->unsigned()->index();
                $table->boolean('is_broadcast')->default(false);
                $table->timestamps();

                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            });
        } else {
            Schema::table('conversations', function (Blueprint $table) {
                if (!Schema::hasColumn('conversations', 'subject')) {
                    $table->string('subject')->nullable()->after('id');
                }
                if (!Schema::hasColumn('conversations', 'created_by')) {
                    $table->integer('created_by')->unsigned()->nullable()->after('subject');
                }
                if (!Schema::hasColumn('conversations', 'is_broadcast')) {
                    $table->boolean('is_broadcast')->default(false);
                }
            });
        }

        if (!Schema::hasTable('conversation_participants')) {
            Schema::create('conversation_participants', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('conversation_id')->unsigned()->index();
                $table->integer('user_id')->unsigned()->index();
                $table->timestamp('last_read_at')->nullable();
                $table->timestamp('created_at')->nullable();

                $table->unique(['conversation_id', 'user_id']);
                $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('conversation_messages')) {
            Schema::create('conversation_messages', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('conversation_id')->unsigned()->index();
                $table->integer('user_id')->unsigned()->index();
                $table->text('body');
                $table->timestamps();

                $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_messages');
        Schema::dropIfExists('conversation_participants');
        Schema::dropIfExists('conversations');
    }
};
