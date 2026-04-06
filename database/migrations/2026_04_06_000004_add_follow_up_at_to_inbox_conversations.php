<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFollowUpAtToInboxConversations extends Migration
{
    public function up()
    {
        Schema::table('inbox_conversations', function (Blueprint $table) {
            $table->timestamp('follow_up_at')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('inbox_conversations', function (Blueprint $table) {
            $table->dropColumn('follow_up_at');
        });
    }
}
