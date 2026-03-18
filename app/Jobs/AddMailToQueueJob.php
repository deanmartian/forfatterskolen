<?php

namespace App\Jobs;

use App\Mail\AddMailToQueueMail;
use App\Repositories\Services\SaleService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class AddMailToQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [60, 300];

    private $recipient;

    private $email_message;

    private $email_subject;

    private $from_name;

    private $from_email;

    private $attach_file;

    private $parent;

    private $parent_id;

    private $email_view;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($recipient, $subject, $message, $from_email, $from_name,
        $attachment, $parent, $parent_id, $email_view = 'emails.mail_to_queue_branded')
    {
        $this->recipient = $recipient;
        $this->email_subject = $subject;
        $this->email_message = $message;
        $this->from_email = $from_email ?: config('mail.from.address', 'support@forfatterskolen.no');
        $this->from_name = $from_name ?: config('mail.from.name', 'Forfatterskolen');
        $this->attach_file = $attachment;
        $this->parent = $parent;
        $this->parent_id = $parent_id;
        $this->email_view = $email_view;
    }

    /**
     * Execute the job.
     */
    public function handle(SaleService $saleService): void
    {
        $track_code = Str::random(32);
        \Mail::send(new AddMailToQueueMail($this->recipient, $this->email_subject, $this->email_message, $this->from_email,
            $this->from_name, $this->attach_file, $track_code, $this->email_view));

        $saleService->createEmailHistory($this->email_subject, $this->from_email, $this->email_message, $this->parent,
            $this->parent_id, $this->recipient, $track_code);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('AddMailToQueueJob failed', [
            'recipient' => $this->recipient,
            'subject' => $this->email_subject,
            'parent' => $this->parent,
            'parent_id' => $this->parent_id,
            'error' => $exception->getMessage(),
        ]);
    }
}
