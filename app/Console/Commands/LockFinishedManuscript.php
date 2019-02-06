<?php

namespace App\Console\Commands;

use App\CronLog;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
use App\Invoice;
use App\ShopManuscriptsTaken;
use Carbon\Carbon;
use Illuminate\Console\Command;

class LockFinishedManuscript extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lockfinishedmanuscript:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lock finished manuscript';

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
     *
     * @return mixed
     */
    public function handle()
    {
        CronLog::create(['activity' => 'LockFinishedManuscript CRON running.']);
        $manuscriptsTakenList = ShopManuscriptsTaken::whereNotNull('file')->get();
        foreach ($manuscriptsTakenList as $manuscriptTaken) {
            if ($manuscriptTaken->feedbacks->count() > 0) {
                $manuscriptTaken->is_manuscript_locked = 1;
                $manuscriptTaken->save();

                CronLog::create(['activity' => 'LockFinishedManuscript CRON updated manuscript taken id '.$manuscriptTaken->id]);
            }
        }
        CronLog::create(['activity' => 'LockFinishedManuscript CRON done running.']);
    }
}
