<?php

namespace Database\Seeders;

use App\Models\category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{

    public function run(): void
    {
        $categories = [
            'work',
            'personal',
            'projects',
            'education'
        ];
        foreach ($categories as $category) {
            category::create(['name' => $category]);
        }
    }
}
