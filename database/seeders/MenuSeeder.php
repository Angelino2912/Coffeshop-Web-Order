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
            ['name' => 'Nasi Goreng Special', 'category' => 'Makanan', 'price' => 25000, 'image' => 'Image/Nasi-goreng.jpg'],
            ['name' => 'Nasi Goreng Daging Rusa', 'category' => 'Makanan', 'price' => 32000, 'image' => 'Image/Nasi Goreng Daging Rusa.jpg'],
            ['name' => 'Mie Ayam Jamur', 'category' => 'Makanan', 'price' => 20000, 'image' => 'Image/mie-ayam-jamur.jpg'],
            ['name' => 'Ayam Geprek', 'category' => 'Makanan', 'price' => 28000, 'image' => 'Image/Ayam-geprek.jpg'],
            ['name' => 'Sate Ayam', 'category' => 'Makanan', 'price' => 22000, 'image' => 'Image/sate-ayam.jpg'],
            ['name' => 'Bakso Urat', 'category' => 'Makanan', 'price' => 18000, 'image' => 'Image/Bakso-urat.jpg'],
            ['name' => 'Rendang Daging', 'category' => 'Makanan', 'price' => 35000, 'image' => 'Image/rendang-daging.jpg'],
            ['name' => 'Gado-Gado', 'category' => 'Makanan', 'price' => 15000, 'image' => 'Image/Gado-gado.jpg'],
            ['name' => 'Soto Ayam', 'category' => 'Makanan', 'price' => 20000, 'image' => 'Image/soto-ayam.jpg'],
            ['name' => 'Nasi Padang', 'category' => 'Makanan', 'price' => 30000, 'image' => 'Image/Nasi-padang.jpg'],
            ['name' => 'Ayam Bakar', 'category' => 'Makanan', 'price' => 25000, 'image' => 'Image/Ayam-bakar.jpg'],

            // Minuman
            ['name' => 'Es Teh Manis', 'category' => 'Minuman', 'price' => 8000, 'image' => 'Image/Es-Teh-Manis.jpg'],
            ['name' => 'Cappuccino', 'category' => 'Minuman', 'price' => 18000, 'image' => 'Image/Cappucino.jpg'],
            ['name' => 'Jus Jambu', 'category' => 'Minuman', 'price' => 15000, 'image' => 'Image/Jus-jambu.jpg'],
            ['name' => 'Es Jeruk', 'category' => 'Minuman', 'price' => 12000, 'image' => 'Image/Jus-jeruk.jpg'],
            ['name' => 'Kopi Hitam', 'category' => 'Minuman', 'price' => 10000, 'image' => 'Image/Kopi-hitam.jpg'],
            ['name' => 'Milkshake Coklat', 'category' => 'Minuman', 'price' => 20000, 'image' => 'Image/Milkshake-coklat.jpg'],
            ['name' => 'Teh Tarik', 'category' => 'Minuman', 'price' => 14000, 'image' => 'Image/Teh-tarik.jpg'],
            ['name' => 'Smoothie Buah', 'category' => 'Minuman', 'price' => 18000, 'image' => 'Image/Smoothie-buah.jpg'],
            ['name' => 'Air Mineral', 'category' => 'Minuman', 'price' => 5000, 'image' => 'Image/Air-mineral.jpg'],
            ['name' => 'Es Kelapa Muda', 'category' => 'Minuman', 'price' => 16000, 'image' => 'Image/Es-kelapa-muda.jpg'],
            ['name' => 'Milo Susu Dingin', 'category' => 'Minuman', 'price' => 15000, 'image' => 'Image/Milo-susu-dingin.jpg'],
            ['name' => 'Ice Land', 'category' => 'Minuman', 'price' => 17000, 'image' => 'Image/iceland.jpg'],

            // Snack
            ['name' => 'Kentang Goreng', 'category' => 'Snack', 'price' => 15000, 'image' => 'Image/Kentang-Goreng.jpg'],
            ['name' => 'Tela Tela', 'category' => 'Snack', 'price' => 12000, 'image' => 'Image/Tela-ela.jpg'],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(
                ['name' => $menu['name']],
                [
                    'category' => $menu['category'],
                    'price'    => $menu['price'],
                    'image'    => $menu['image'],
                ]
            );
        }
    }
}
