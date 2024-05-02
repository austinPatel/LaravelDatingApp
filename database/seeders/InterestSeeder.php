<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Interest;

class InterestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $interests = [
            [
                'name' => 'Art',
                'type' => 1
            ],
            [
                'name' => 'Crafts',
                'type' => 1
            ],
            [
                'name' => 'Dancing',
                'type' => 1
            ],
            [
                'name' => 'Photography',
                'type' => 1
            ],
            [
                'name' => 'Writing',
                'type' => 1
            ],
            [
                'name' => 'AFL',
                'type' => 2
            ],
            [
                'name' => 'Rugby',
                'type' => 2
            ],
            [
                'name' => 'Cricket',
                'type' => 2
            ],
            [
                'name' => 'Yoga',
                'type' => 2
            ],
            [
                'name' => 'Gym',
                'type' => 2
            ],
            [
                'name' => 'Swimming',
                'type' => 2
            ],
            [
                'name' => 'Bowling',
                'type' => 2
            ],
            [
                'name' => 'Staying in',
                'type' => 3
            ],
            [
                'name' => 'Going out',
                'type' => 3
            ],
            [
                'name' => 'The Beach',
                'type' => 3
            ],
            [
                'name' => 'Anime',
                'type' => 3
            ],
            [
                'name' => 'Cafes',
                'type' => 3
            ],
            [
                'name' => 'Movies',
                'type' => 3
            ],
            [
                'name' => 'Live music',
                'type' => 3
            ],
            [
                'name' => 'Animals',
                'type' => 3
            ],
            [
                'name' => 'Religion',
                'type' => 3
            ],
            [
                'name' => 'Politics',
                'type' => 3
            ],
            [
                'name' => 'Being active',
                'type' => 3
            ],
            [
                'name' => 'Positivity',
                'type' => 3
            ],
            [
                'name' => 'Being family-orientated',
                'type' => 3
            ],
            [
                'name' => 'Being Romantic',
                'type' => 3
            ],
            [
                'name' => 'Sense of humour',
                'type' => 3
            ]
        ];

        foreach ($interests as $key => $interest) {
            Interest::create([
                'name' => $interest['name'],
                'type' => $interest['type']
            ]);
        }
    }
}
