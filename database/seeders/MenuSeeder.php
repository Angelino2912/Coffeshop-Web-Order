<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            // Makanan
            ['name' => 'Nasi Goreng Special', 'category' => 'Makanan', 'price' => 25000],
            ['name' => 'Mie Ayam Jamur', 'category' => 'Makanan', 'price' => 20000],
            ['name' => 'Ayam Geprek', 'category' => 'Makanan', 'price' => 28000],
            ['name' => 'Sate Ayam', 'category' => 'Makanan', 'price' => 22000],
            ['name' => 'Bakso Urat', 'category' => 'Makanan', 'price' => 18000],
            ['name' => 'Rendang Daging', 'category' => 'Makanan', 'price' => 35000],
            ['name' => 'Gado-Gado', 'category' => 'Makanan', 'price' => 15000],
            ['name' => 'Soto Ayam', 'category' => 'Makanan', 'price' => 20000],
            ['name' => 'Nasi Padang', 'category' => 'Makanan', 'price' => 30000],
            ['name' => 'Ayam Bakar', 'category' => 'Makanan', 'price' => 25000],
            // Minuman
            ['name' => 'Es Teh Manis', 'category' => 'Minuman', 'price' => 8000],
            ['name' => 'Cappuccino', 'category' => 'Minuman', 'price' => 18000],
            ['name' => 'Jus Jambu', 'category' => 'Minuman', 'price' => 15000],
            ['name' => 'Es Jeruk', 'category' => 'Minuman', 'price' => 12000],
            ['name' => 'Kopi Hitam', 'category' => 'Minuman', 'price' => 10000],
            ['name' => 'Milkshake Coklat', 'category' => 'Minuman', 'price' => 20000],
            ['name' => 'Teh Tarik', 'category' => 'Minuman', 'price' => 14000],
            ['name' => 'Smoothie Buah', 'category' => 'Minuman', 'price' => 18000],
            ['name' => 'Air Mineral', 'category' => 'Minuman', 'price' => 5000],
            ['name' => 'Es Kelapa Muda', 'category' => 'Minuman', 'price' => 16000],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(
                ['name' => $menu['name']],
                ['category' => $menu['category'], 'price' => $menu['price']]
            );
        }
    }
}
