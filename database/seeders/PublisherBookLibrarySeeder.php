<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PublisherBookLibrarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $publisherBooks = \App\PublisherBook::all();
        $counter = 0;
        foreach ($publisherBooks as $publisherBook) {
            $publisherBook->libraries()->create([
                'book_image' => $publisherBook->book_image,
                'book_link' => $publisherBook->book_image_link,
            ]);
            $counter++;
        }
        echo $counter." books inserted to library \n";
    }
}
