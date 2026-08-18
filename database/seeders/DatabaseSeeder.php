<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);

        $fruits = Category::create(['name' => 'Fruits', 'slug' => 'fruits']);
        $drinks = Category::create(['name' => 'Drinks', 'slug' => 'drinks']);

        Product::create([
            'category_id' => $fruits->id,
            'name' => 'Apple',
            'barcode' => '10001',
            'price' => 1.50,
            'stock_quantity' => 100,
        ]);
        Product::create([
            'category_id' => $fruits->id,
            'name' => 'Banana',
            'barcode' => '10002',
            'price' => 0.80,
            'stock_quantity' => 150,
        ]);
        Product::create([
            'category_id' => $drinks->id,
            'name' => 'Coca Cola',
            'barcode' => '10003',
            'price' => 2.00,
            'stock_quantity' => 50,
        ]);
        Product::create([
            'category_id' => $drinks->id,
            'name' => 'Water',
            'barcode' => '10004',
            'price' => 1.00,
            'stock_quantity' => 200,
        ]);
    }
}
