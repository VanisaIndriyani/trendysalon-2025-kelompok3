<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HairVitamin;

class HairVitaminsTableSeeder extends Seeder
{
    public function run(): void
    {
        // Data vitamin rambut asli yang umum digunakan untuk perawatan rambut
        $items = [
            // Vitamin untuk Rambut Sehat
            ['name' => 'Biotin', 'hair_type' => 'Sehat'],
            ['name' => 'Vitamin E', 'hair_type' => 'Sehat'],
            ['name' => 'Vitamin D', 'hair_type' => 'Sehat'],
            ['name' => 'Vitamin B Complex', 'hair_type' => 'Sehat'],
            ['name' => 'Collagen', 'hair_type' => 'Sehat'],
            ['name' => 'Keratin', 'hair_type' => 'Sehat'],
            ['name' => 'Zinc', 'hair_type' => 'Sehat'],
            ['name' => 'Iron (Zat Besi)', 'hair_type' => 'Sehat'],
            
            // Vitamin untuk Rambut Kering
            ['name' => 'Minyak Argan', 'hair_type' => 'Kering'],
            ['name' => 'Minyak Jojoba', 'hair_type' => 'Kering'],
            ['name' => 'Minyak Kelapa', 'hair_type' => 'Kering'],
            ['name' => 'Minyak Zaitun', 'hair_type' => 'Kering'],
            ['name' => 'Vitamin A', 'hair_type' => 'Kering'],
            ['name' => 'Omega-3', 'hair_type' => 'Kering'],
            ['name' => 'Hyaluronic Acid', 'hair_type' => 'Kering'],
            ['name' => 'Shea Butter', 'hair_type' => 'Kering'],
            
            // Vitamin untuk Rambut Rusak
            ['name' => 'Protein Treatment', 'hair_type' => 'Rusak'],
            ['name' => 'Keratin Treatment', 'hair_type' => 'Rusak'],
            ['name' => 'Amino Acids', 'hair_type' => 'Rusak'],
            ['name' => 'Panthenol (Vitamin B5)', 'hair_type' => 'Rusak'],
            ['name' => 'Niacin (Vitamin B3)', 'hair_type' => 'Rusak'],
            ['name' => 'Folic Acid', 'hair_type' => 'Rusak'],
            ['name' => 'Silica', 'hair_type' => 'Rusak'],
            ['name' => 'MSM (Methylsulfonylmethane)', 'hair_type' => 'Rusak'],
            
            // Vitamin untuk Rambut Lurus
            ['name' => 'Smoothing Serum', 'hair_type' => 'Lurus'],
            ['name' => 'Straightening Treatment', 'hair_type' => 'Lurus'],
            ['name' => 'Anti-Frizz Serum', 'hair_type' => 'Lurus'],
            
            // Vitamin untuk Rambut Ikal/Bergelombang
            ['name' => 'Curl Defining Cream', 'hair_type' => 'Ikal'],
            ['name' => 'Curl Enhancing Serum', 'hair_type' => 'Ikal'],
            ['name' => 'Moisturizing Mask', 'hair_type' => 'Bergelombang'],
            ['name' => 'Curl Activator', 'hair_type' => 'Bergelombang'],
        ];

        foreach ($items as $it) {
            HairVitamin::updateOrCreate([
                'name' => $it['name'],
            ], [
                'hair_type' => $it['hair_type'],
            ]);
        }
        
        $this->command->info('✅ HairVitamin seeder berhasil! Total: ' . count($items) . ' vitamin rambut.');
    }
}