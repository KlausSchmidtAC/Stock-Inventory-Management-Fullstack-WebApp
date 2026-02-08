<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Erst Kategorien erstellen
        $categories = [
            'Smartphones',
            'Laptops',
            'Tablets',
            'Monitore',
            'Tastaturen',
            'Mäuse',
            'Headsets',
            'Webcams',
            'Lautsprecher',
            'Drucker',
        ];

        foreach ($categories as $categoryName) {
            \App\Models\Category::create(['name' => $categoryName]);
        }

        // Dann Produkte mit zufälligen existierenden Kategorien
        Product::factory(50)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('12345'),
        ]);
        User::factory()->create([
            'name' => 'Admin User',
            'email' =>'admin@example.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

    }
}
