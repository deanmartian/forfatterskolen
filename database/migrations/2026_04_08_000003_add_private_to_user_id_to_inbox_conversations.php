<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Legger til `private_to_user_id` på inbox_conversations slik at vi
 * kan rute innkommende e-post til en bestemt admin-bruker sin private
 * inbox — f.eks. sven.inge@forfatterskolen.no går kun til Sven Inge.
 *
 * Null = offentlig inbox (alle admins ser den).
 * Verdi = privat inbox (kun den bestemte admin-brukeren ser den).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inbox_conversations', function (Blueprint $table) {
            $table->unsignedBigInteger('private_to_user_id')->nullable()->after('inbox');
            $table->index('private_to_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('inbox_conversations', function (Blueprint $table) {
            $table->dropIndex(['private_to_user_id']);
            $table->dropColumn('private_to_user_id');
        });
    }
};
