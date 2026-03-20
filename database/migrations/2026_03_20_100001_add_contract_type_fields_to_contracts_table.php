<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->enum('contract_type', ['firma', 'person'])->nullable()->after('status');
            $table->string('org_nr')->nullable()->after('contract_type');
            $table->string('fodselsnummer')->nullable()->after('org_nr');
            $table->string('mobile')->nullable()->after('fodselsnummer');
            $table->decimal('timepris', 8, 2)->nullable()->after('mobile');
            $table->date('start_date')->nullable()->after('timepris');
            $table->boolean('reminder_sent_60')->default(false)->after('start_date');
            $table->boolean('reminder_sent_30')->default(false)->after('reminder_sent_60');
            $table->unsignedBigInteger('renewed_from_id')->nullable()->after('reminder_sent_30');
            $table->string('receiver_address')->nullable()->after('receiver_email');
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn([
                'contract_type', 'org_nr', 'fodselsnummer', 'mobile',
                'timepris', 'start_date', 'reminder_sent_60', 'reminder_sent_30',
                'renewed_from_id', 'receiver_address',
            ]);
        });
    }
};
