<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Colocation;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colocations = Colocation::all();

        foreach ($colocations as $colocation) {
            $categories = ['Rent', 'Food', 'Utilities', 'Leisure', 'Cleaning'];

            foreach ($categories as $name) {
                Category::firstOrCreate([
                    'colocation_id' => $colocation->id,
                    'name' => $name,
                ]);
            }
        }
    }
}
