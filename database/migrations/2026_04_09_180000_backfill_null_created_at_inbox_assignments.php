<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Rydder opp etter regresjonen der auto-tildelingen i PollInboxEmails
 * opprettet InboxAssignment-rader uten created_at (fordi modellen har
 * $timestamps = false og cron-koden glemte å sette feltet manuelt).
 *
 * Konsekvensen var at show.blade.php krasjet med "Call to a member
 * function format() on null" så snart man åpnet en samtale med
 * slike rader. Denne migrasjonen gir alle NULL-rader en created_at
 * lik samtalens updated_at (eller nå, som nødløsning), slik at
 * viewet kan rendre dem igjen.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Fallback-rekkefølge: samtalens updated_at → samtalens created_at → NOW()
        DB::statement("
            UPDATE inbox_assignments ia
            LEFT JOIN inbox_conversations ic ON ic.id = ia.conversation_id
            SET ia.created_at = COALESCE(ic.updated_at, ic.created_at, NOW())
            WHERE ia.created_at IS NULL
        ");
    }

    public function down(): void
    {
        // Ingen revers — vi vet ikke hvilke rader som opprinnelig var NULL.
    }
};
