<?php

namespace Database\Seeders;

use App\Models\Difficulty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DifficultiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        Difficulty::create(['name'=>'easy', 'score'=>10]);
        Difficulty::create(['name'=>'mid', 'score'=>20]);
        Difficulty::create(['name'=>'hard','score'=>30]);

    }
}
