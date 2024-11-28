<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories=[
            'general_culture',
            'history',
            'sports',
            'math',
            'science',
            'geography',
            'islamic',
            'puzzles'
        ];
        foreach ($categories as $category){
            Category::create(['name'=>$category]);
        }
    }
}
