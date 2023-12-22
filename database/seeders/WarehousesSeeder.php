<?php

namespace Database\Seeders;

use App\Models\Warehouses;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehousesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Warehouses::create([
            'material_id' => 1,
            'reminder' => 12,
            'price' => 1500,
        ]);

        Warehouses::create([
            'material_id' => 1,
            'reminder' => 200,
            'price' => 1600,
        ]);

        Warehouses::create([
            'material_id' => 2,
            'reminder' => 40,
            'price' => 500,
        ]);

        Warehouses::create([
            'material_id' => 2,
            'reminder' => 300,
            'price' => 550,
        ]);

        Warehouses::create([
            'material_id' => 3,
            'reminder' => 500,
            'price' => 300,
        ]);

        Warehouses::create([
            'material_id' => 3,
            'reminder' => 1000,
            'price' => 2000,
        ]);

    }
}
