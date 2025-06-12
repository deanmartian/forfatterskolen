<?php

namespace App\Jobs;

use App\Mail\CourseOrderMail;
use App\Repositories\Services\SaleService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CourseOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $recipient;

    private $email_content;

    private $email_subject;

    private $from_name;

    private $from_email;

    private $attach_file;

    private $parent;

    private $parent_id;

    private $actionText;

    private $actionUrl;

    private $user;

    private $package_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($recipient, $subject, $message, $from_email, $from_name,
        $attachment, $parent, $parent_id, $actionText, $actionUrl, $user, $package_id)
    {
        $this->recipient = $recipient;
        $this->email_subject = $subject;
        $this->email_content = $message;
        $this->from_email = $from_email ?: 'postmail@forfatterskolen.no';
        $this->from_name = $from_name ?: 'Forfatterskolen';
        $this->attach_file = $attachment;
        $this->parent = $parent;
        $this->parent_id = $parent_id;
        $this->actionText = $actionText;
        $this->actionUrl = $actionUrl;
        $this->user = $user;
        $this->package_id = $package_id;
    }

    /**
     * Execute the job.
     */
    public function handle(SaleService $saleService): void
    {

        $track_code = md5(rand());
        \Mail::send(new CourseOrderMail($this->recipient, $this->email_subject, $this->email_content, $this->from_email,
            $this->from_name, $this->attach_file, $track_code, $this->actionText, $this->actionUrl, $this->user,
            $this->package_id));

        $saleService->createEmailHistory($this->email_subject, $this->from_email, $this->email_content, $this->parent,
            $this->parent_id, $this->recipient, $track_code);

    }
}
