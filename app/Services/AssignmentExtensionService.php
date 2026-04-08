<?php

namespace App\Services;

use App\Jobs\AddMailToQueueJob;
use App\Models\AssignmentExtensionRequest;
use App\User;

/**
 * Håndterer godkjenning/avslag av fristforlengelser for innleveringer.
 *
 * Finnes for å unngå duplisert kode mellom admin-controlleren og
 * AI-tool-et (ApproveExtensionRequestTool). Begge kaller samme metoder
 * her — så forretningslogikken bor på ett sted.
 */
class AssignmentExtensionService
{
    /**
     * Godkjenn en fristforlengelse.
     * Oppdaterer status, setter ny frist, og sender e-post til eleven.
     *
     * Returnerer det oppdaterte request-et, eller null hvis forespørselen
     * ikke finnes eller allerede er behandlet.
     */
    public function approve(int $extensionRequestId, int $decidedByUserId): ?AssignmentExtensionRequest
    {
        $req = AssignmentExtensionRequest::find($extensionRequestId);

        if (!$req || $req->status !== 'pending') {
            return null;
        }

        $student = $req->user;
        $assignment = $req->assignment;

        $req->update([
            'status' => 'approved',
            'decided_by' => $decidedByUserId,
            'decided_at' => now(),
        ]);

        \App\AssignmentLearnerSubmissionDate::updateOrCreate(
            ['assignment_id' => $req->assignment_id, 'user_id' => $req->user_id],
            ['submission_date' => $req->requested_deadline->format('M d, Y h:i A')]
        );

        if ($student && $assignment) {
            $subject = 'Utsettelse godkjent - ' . $assignment->title;
            $body = '<p>Hei ' . $student->fullname . ',</p>'
                . '<p>Utsettelsen din for oppgaven <strong>' . $assignment->title . '</strong> er godkjent.</p>'
                . '<p>Ny frist: <strong>' . $req->requested_deadline->format('d.m.Y') . '</strong></p>'
                . '<p>Vennlig hilsen,<br>Forfatterskolen</p>';

            dispatch(new AddMailToQueueJob(
                $student->email,
                $subject,
                $body,
                'post@forfatterskolen.no',
                'Forfatterskolen',
                null,
                'assignment_extension',
                $req->id
            ));
        }

        return $req->fresh();
    }

    /**
     * Avslå en fristforlengelse.
     */
    public function reject(int $extensionRequestId, int $decidedByUserId): ?AssignmentExtensionRequest
    {
        $req = AssignmentExtensionRequest::find($extensionRequestId);

        if (!$req || $req->status !== 'pending') {
            return null;
        }

        $student = $req->user;
        $assignment = $req->assignment;

        $req->update([
            'status' => 'rejected',
            'decided_by' => $decidedByUserId,
            'decided_at' => now(),
        ]);

        if ($student && $assignment) {
            $subject = 'Utsettelse avslått - ' . $assignment->title;
            $body = '<p>Hei ' . $student->fullname . ',</p>'
                . '<p>Utsettelsen for oppgaven <strong>' . $assignment->title . '</strong> ble dessverre ikke godkjent.</p>'
                . '<p>Opprinnelig frist gjelder fortsatt.</p>'
                . '<p>Vennlig hilsen,<br>Forfatterskolen</p>';

            dispatch(new AddMailToQueueJob(
                $student->email,
                $subject,
                $body,
                'post@forfatterskolen.no',
                'Forfatterskolen',
                null,
                'assignment_extension',
                $req->id
            ));
        }

        return $req->fresh();
    }
}
