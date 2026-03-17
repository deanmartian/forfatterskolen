<?php

namespace App\Providers;

use App\EmailLog;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Mail\Events\MessageSent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        'App\Events\AddToCampaignList' => [
            'App\Listeners\AddToCampaignListListener',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();

        // Log all outgoing emails
        \Illuminate\Support\Facades\Event::listen(MessageSent::class, function (MessageSent $event) {
            try {
                $message = $event->message;
                $to = $message->getTo();
                $toEmail = '';
                $toName = null;

                if (is_array($to)) {
                    foreach ($to as $address) {
                        $toEmail = $address->getAddress();
                        $toName = $address->getName();
                        break;
                    }
                }

                EmailLog::create([
                    'to_email' => $toEmail,
                    'to_name' => $toName,
                    'subject' => $message->getSubject() ?? '',
                    'mailable_class' => $event->data['__laravel_notification'] ?? get_class($event->sent),
                    'status' => 'sent',
                ]);
            } catch (\Throwable $e) {
                // Don't break email sending if logging fails
                \Log::warning('Email log failed: ' . $e->getMessage());
            }
        });
    }
}
