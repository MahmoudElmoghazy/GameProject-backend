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
        Difficulty::create(['name'=>'easy']);
        Difficulty::create(['name'=>'mid']);
        Difficulty::create(['name'=>'hard']);

    }
}
