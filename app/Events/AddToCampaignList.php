<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddToCampaignList
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $list_id;

    public $listData;

    /**
     * Create a new event instance.
     * AddToCampaignList constructor.
     */
    public function __construct($list_id, $listData)
    {
        $this->list_id = $list_id;
        $this->listData = $listData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
