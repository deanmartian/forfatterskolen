<?php

namespace App\Jobs;

use App\Mail\AddMailToQueueMail;
use App\Repositories\Services\SaleService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddMailToQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $attachment, $parent, $parent_id, $email_view = 'emails.mail_to_queue')
    {
        $this->recipient = $recipient;
        $this->email_subject = $subject;
        $this->email_message = $message;
        $this->from_email = $from_email ?: 'postmail@forfatterskolen.no';
        $this->from_name = $from_name ?: 'Forfatterskolen';
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

        $track_code = md5(rand());
        \Mail::send(new AddMailToQueueMail($this->recipient, $this->email_subject, $this->email_message, $this->from_email,
            $this->from_name, $this->attach_file, $track_code, $this->email_view));

        $saleService->createEmailHistory($this->email_subject, $this->from_email, $this->email_message, $this->parent,
            $this->parent_id, $this->recipient, $track_code);

    }
}
