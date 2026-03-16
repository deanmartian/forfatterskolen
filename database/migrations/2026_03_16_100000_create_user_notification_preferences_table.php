<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserNotificationPreferencesTable extends Migration
{
    public function up()
    {
        Schema::create('user_notification_preferences', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->string('type', 50);
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_notification_preferences');
    }
}
