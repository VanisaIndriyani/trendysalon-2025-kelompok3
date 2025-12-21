<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HairModel;

class HairModelSeeder extends Seeder
{
    public function run(): void
    {
        // Data model rambut sesuai dengan foto yang ada di img/model/panjang dan img/model/pendek
        $data = [
            // Model Panjang - sesuai dengan foto di img/model/panjang
            ['name' => 'Blunt Cut', 'image' => 'img/model/panjang/Blunt Cut.jpg', 'types' => 'Lurus', 'length' => 'Panjang', 'face_shapes' => 'Oval,Round,Square'],
            ['name' => 'Curtain Bangs', 'image' => 'img/model/panjang/Curtain Bangs.png', 'types' => 'Lurus, Ikal', 'length' => 'Panjang', 'face_shapes' => 'Oval,Round,Heart'],
            ['name' => 'Face-Framing Layers', 'image' => 'img/model/panjang/Face-Framing Layers.jpg', 'types' => 'Lurus, Ikal, Bergelombang', 'length' => 'Panjang', 'face_shapes' => 'Oval,Round,Heart'],
            ['name' => 'Full Bangs', 'image' => 'img/model/panjang/Full Bangs.jpg', 'types' => 'Lurus, Bergelombang', 'length' => 'Panjang', 'face_shapes' => 'Oval,Round,Square'],
            ['name' => 'Long and Sleek', 'image' => 'img/model/panjang/Long and Sleek.jpg', 'types' => 'Lurus', 'length' => 'Panjang', 'face_shapes' => 'Oval,Round,Oblong'],
            ['name' => 'Long Layers', 'image' => 'img/model/panjang/Long Layers.jpg', 'types' => 'Lurus, Bergelombang', 'length' => 'Panjang', 'face_shapes' => 'Oval,Round,Heart'],
            ['name' => 'Long Shag', 'image' => 'img/model/panjang/Long Shag.jpg', 'types' => 'Lurus, Bergelombang', 'length' => 'Panjang', 'face_shapes' => 'Oval,Round,Heart'],
            ['name' => 'Mid-back Length', 'image' => 'img/model/panjang/Mid-back Length.jpg', 'types' => 'Lurus, Ikal', 'length' => 'Panjang', 'face_shapes' => 'Oval,Round,Square'],
            ['name' => 'U-Cut', 'image' => 'img/model/panjang/U-Cut.jpg', 'types' => 'Lurus, Bergelombang', 'length' => 'Panjang', 'face_shapes' => 'Oval,Round,Heart'],
            ['name' => 'Wavy Cut', 'image' => 'img/model/panjang/Wavy Cut.jpg', 'types' => 'Bergelombang, Ikal', 'length' => 'Panjang', 'face_shapes' => 'Round,Heart,Oval'],
            
            // Model Pendek - sesuai dengan foto di img/model/pendek
            ['name' => 'A-Line Bob', 'image' => 'img/model/pendek/A-Line Bob.jpg', 'types' => 'Lurus', 'length' => 'Pendek', 'face_shapes' => 'Oval,Round,Square'],
            ['name' => 'Bob Klasik', 'image' => 'img/model/pendek/Bob Klasik.jpg', 'types' => 'Lurus, Bergelombang', 'length' => 'Pendek', 'face_shapes' => 'Round,Square,Oval'],
            ['name' => 'Curtain Bangs Bob', 'image' => 'img/model/pendek/Curtain Bangs Bob.jpg', 'types' => 'Lurus', 'length' => 'Pendek', 'face_shapes' => 'Oval,Round,Heart'],
            ['name' => 'Layered Bob', 'image' => 'img/model/pendek/Layered Bob.jpg', 'types' => 'Lurus, Ikal', 'length' => 'Pendek', 'face_shapes' => 'Oval,Round,Square'],
            ['name' => 'Long Bob', 'image' => 'img/model/pendek/Long Bob.jpeg', 'types' => 'Lurus, Bergelombang', 'length' => 'Pendek', 'face_shapes' => 'Oval,Round'],
            ['name' => 'Pixie Cut Poni', 'image' => 'img/model/pendek/Pixie Cut Poni.jpg', 'types' => 'Lurus', 'length' => 'Pendek', 'face_shapes' => 'Square,Heart,Round'],
            ['name' => 'Pixie Cut', 'image' => 'img/model/pendek/Pixie Cut.png', 'types' => 'Lurus, Bergelombang', 'length' => 'Pendek', 'face_shapes' => 'Square,Heart,Round'],
            ['name' => 'Shaggy Bob', 'image' => 'img/model/pendek/Shaggy Bob.jpeg', 'types' => 'Bergelombang', 'length' => 'Pendek', 'face_shapes' => 'Round,Heart,Oval'],
            ['name' => 'Sleek Bob', 'image' => 'img/model/pendek/Sleek Bob.jpg', 'types' => 'Lurus', 'length' => 'Pendek', 'face_shapes' => 'Round,Square,Oval'],
            ['name' => 'Wavy Bob', 'image' => 'img/model/pendek/Wavy Bob.jpg', 'types' => 'Bergelombang', 'length' => 'Pendek', 'face_shapes' => 'Round,Heart,Oval'],
        ];

        foreach ($data as $row) {
            HairModel::updateOrCreate(
                ['name' => $row['name']],
                $row
            );
        }
        
        // ✅ HAPUS MODEL YANG TIDAK ADA FOTONYA (path lama seperti img/model1.png)
        $oldModels = HairModel::where('image', 'like', 'img/model%.png')
            ->orWhere('image', 'like', 'img/model%.jpg')
            ->whereNotIn('name', array_column($data, 'name'))
            ->get();
        
        foreach ($oldModels as $oldModel) {
            // Cek apakah file benar-benar tidak ada
            $path = public_path($oldModel->image);
            if (!file_exists($path)) {
                $oldModel->delete();
                $this->command->info("🗑️  Deleted: {$oldModel->name} (missing image: {$oldModel->image})");
            }
        }
        
        // ✅ HAPUS MODEL YANG TIDAK ADA IMAGE ATAU IMAGE KOSONG
        $modelsWithoutImage = HairModel::whereNull('image')
            ->orWhere('image', '')
            ->get();
        
        foreach ($modelsWithoutImage as $model) {
            $model->delete();
            $this->command->info("🗑️  Deleted: {$model->name} (no image)");
        }
        
        // ✅ UPDATE MODEL YANG NAMANYA SAMA TAPI PATH FOTONYA SALAH
        foreach ($data as $row) {
            $model = HairModel::where('name', $row['name'])->first();
            if ($model && $model->image !== $row['image']) {
                // Cek apakah file baru ada
                $newPath = public_path($row['image']);
                if (file_exists($newPath)) {
                    $model->update(['image' => $row['image']]);
                    $this->command->info("🔄 Updated: {$model->name} - {$row['image']}");
                }
            }
        }
        
        $this->command->info('✅ HairModel seeder berhasil! Total: ' . count($data) . ' model rambut.');
    }
}