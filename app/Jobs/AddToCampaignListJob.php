<?php

namespace App\Jobs;

use App\Http\AdminHelpers;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AddToCampaignListJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $list_id;
    private $listData;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($list_id, $listData)
    {
        $this->list_id = $list_id;
        $this->listData = $listData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        AdminHelpers::addToActiveCampaignList($this->list_id, $this->listData);
    }
}
