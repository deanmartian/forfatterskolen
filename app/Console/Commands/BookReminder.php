<?php

namespace App\Console\Commands;

use App\Http\AdminHelpers;
use App\PilotReaderBookReading;
use App\PilotReaderBookSettings;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BookReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookreminder:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Book reminder email send';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $settings = PilotReaderBookSettings::where('is_reading_reminder_on', 1)->get();
        foreach ($settings as $setting) {
            $reminder_days = $setting->days_of_reminder;
            $readers = PilotReaderBookReading::where(['book_id' => $setting->book_id])->get();
            foreach ($readers as $reader) {
                // format the last seen first to remove time on comparison
                $last_seen = Carbon::parse($reader->last_seen)->format('Y-m-d');
                $days_diff = Carbon::parse($last_seen)->diffInDays(Carbon::today());

                // check if the day difference is greater than the set reminder days
                if ($days_diff > $reminder_days) {
                    $book = $reader->book;
                    $to = $reader->user->email;
                    $subject = 'Reading reminder for '.$book->title;

                    $email_data = [
                        'receiver' => $reader->user->first_name,
                        'book_title' => $book->title,
                        'days_diff' => $days_diff,
                        'book_author' => $book->author->full_name,
                        'book_link' => route('learner.book-author-book-show', $book->id),
                    ];

                    AdminHelpers::send_mail($to, $subject,
                        view('emails.book_reminder', compact('email_data')), 'no-reply@forfatterskolen.no');
                }
            }
        }
    }
}
