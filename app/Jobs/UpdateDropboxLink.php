<?php

namespace App\Jobs;

use App\ProjectWholeBook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Spatie\Dropbox\Client;

class UpdateDropboxLink implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $projectWholeBook;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ProjectWholeBook $projectWholeBook)
    {
        $this->projectWholeBook = $projectWholeBook;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $dropboxClient = new Client(config('filesystems.disks.dropbox.authorization_token'));
        try {
            $path = $this->projectWholeBook->book_content;
            $response = $dropboxClient->createSharedLinkWithSettings($path, [
                'requested_visibility' => 'public',
            ]);

            if (isset($response['url'])) {
                $this->projectWholeBook->dropbox_link = str_replace('?dl=0', '?raw=1', $response['url']);
                $this->projectWholeBook->save();
                Log::info("Dropbox link for project ID {$this->projectWholeBook->id} updated successfully.");
            } else {
                Log::error("Failed to get Dropbox link for project ID {$this->projectWholeBook->id}. Response: "
                .json_encode($response));
            }
        } catch (\Exception $e) {
            Log::error("Error fetching Dropbox link for project ID {$this->projectWholeBook->id}: ".$e->getMessage());
        }
    }
}
