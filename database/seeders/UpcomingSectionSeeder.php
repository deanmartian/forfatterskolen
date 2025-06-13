<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpcomingSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\UpcomingSection::create([
            'name' => 'Reprise webinar',
            'title' => 'Markedsføring for forfattere',
            'link' => '/reprise',
            'link_label' => 'Les mer',
        ]);

        \App\UpcomingSection::create([
            'name' => 'Neste webinar',
            'title' => 'Camilla Sandmo',
            'date' => '2021-09-20 20:00:00',
            'link' => '/course/17?show_kursplan=1',
            'link_label' => 'Se komplett liste her',
        ]);

        \App\UpcomingSection::create([
            'name' => 'Reprise webinar',
            'title' => 'Marit Reiersgård - Fra tegn til tegning',
            'link' => '/hererjeg',
            'link_label' => 'Les mer',
        ]);
    }
}
