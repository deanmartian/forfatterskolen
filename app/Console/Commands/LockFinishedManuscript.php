<?php

namespace App\Console\Commands;

use App\ShopManuscriptsTaken;
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
     */
    public function handle(): void
    {
        $manuscriptsTakenList = ShopManuscriptsTaken::whereNotNull('file')->get();
        foreach ($manuscriptsTakenList as $manuscriptTaken) {
            if ($manuscriptTaken->feedbacks->count() > 0) {
                $manuscriptTaken->is_manuscript_locked = 1;
                $manuscriptTaken->save();
            }
        }
    }
}
