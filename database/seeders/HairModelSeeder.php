<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HairModel;

class HairModelSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'Oval Layer With Curtain Bangs', 'image' => 'img/model1.png', 'types' => 'Lurus, Ikal, Bergelombang', 'length' => 'Panjang', 'face_shapes' => 'Oval,Round,Oblong'],
            ['name' => 'Butterfly Hair Cut', 'image' => 'img/model2.png', 'types' => 'Lurus, Ikal, Bergelombang', 'length' => 'Panjang', 'face_shapes' => 'Oval,Heart,Oblong'],
            ['name' => 'Wolf Cut Long Hair', 'image' => 'img/model3.png', 'types' => 'Lurus', 'length' => 'Panjang', 'face_shapes' => 'Oval,Round'],
            ['name' => 'Bob Hair Cut', 'image' => 'img/model2.png', 'types' => 'Lurus', 'length' => 'Pendek', 'face_shapes' => 'Round,Square'],
            ['name' => 'Pixie Cut', 'image' => 'img/model1.png', 'types' => 'Lurus, Bergelombang', 'length' => 'Panjang', 'face_shapes' => 'Square,Heart'],
            ['name' => 'Face Framing Layers', 'image' => 'img/model3.png', 'types' => 'Lurus', 'length' => 'Panjang', 'face_shapes' => 'Oval,Oblong'],
            ['name' => 'Wavy Bob', 'image' => 'img/model2.png', 'types' => 'Bergelombang', 'length' => 'Panjang', 'face_shapes' => 'Round,Heart'],
        ];

        foreach ($data as $row) {
            HairModel::updateOrCreate(
                ['name' => $row['name']],
                $row
            );
        }
    }
}