<?php

namespace App\Listeners;

use App\Events\AddToCampaignList;
use App\Jobs\AddToCampaignListJob;

class AddToCampaignListListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AddToCampaignList $event): void
    {
        dispatch(new AddToCampaignListJob($event->list_id, $event->listData));
    }
}
