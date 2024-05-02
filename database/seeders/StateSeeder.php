<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $states =array(
            [
                "name"=>"Australian Capital Territory",
                "code"=>"ACT"
            ],
            [
                "name"=>"New South Wales",
                "code"=>"NSW"
            ],
            [
                "name"=>"Northern Territory",
                "code"=>"NT"
            ],
            [
                "name"=>"Queensland",
                "code"=>"QLD"
            ],
            [
                "name"=>"South Australia",
                "code"=>"SA"
            ],
            [
                "name"=>"Tasmania",
                "code"=>"TAS"
            ],
            [
                "name"=>"Victoria",
                "code"=>"VIC"
            ],
            [
                "name"=>"Western Australia",
                "code"=>"WA"
            ],

        );
        foreach ($states as $key => $state) {
            State::create([
                'name' => $state['name'],
                'code' => $state['code']
            ]);
        }

    }
}
