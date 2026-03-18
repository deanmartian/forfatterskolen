<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletters', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->string('preview_text')->nullable();
            $table->longText('body_html');
            $table->string('from_address')->default('post@nyhetsbrev.forfatterskolen.no');
            $table->string('from_name')->default('Forfatterskolen');
            $table->string('segment')->default('all'); // all, active_course, no_active_course, tag:xxx, webinar_registrants
            $table->string('status')->default('draft'); // draft, scheduled, sending, sent, cancelled
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('total_sent')->default(0);
            $table->integer('total_failed')->default(0);
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletters');
    }
};
