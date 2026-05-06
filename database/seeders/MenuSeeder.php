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
            ['name' => 'Nasi Goreng Special', 'category' => 'Makanan', 'price' => 25000],
            ['name' => 'Mie Ayam Jamur', 'category' => 'Makanan', 'price' => 20000],
            ['name' => 'Ayam Geprek', 'category' => 'Makanan', 'price' => 28000],
            ['name' => 'Es Teh Manis', 'category' => 'Minuman', 'price' => 8000],
            ['name' => 'Cappuccino', 'category' => 'Minuman', 'price' => 18000],
            ['name' => 'Jus Jambu', 'category' => 'Minuman', 'price' => 15000],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(
                ['name' => $menu['name']],
                ['category' => $menu['category'], 'price' => $menu['price']]
            );
        }
    }
}
