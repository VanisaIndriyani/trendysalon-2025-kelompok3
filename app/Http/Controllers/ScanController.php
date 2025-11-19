<?php

namespace App\Http\Controllers;

use App\Models\HairOption;
use App\Models\HairModel;
use App\Models\Recommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ScanController extends Controller
{
    public function analyze(Request $request)
    {
        $dataUrl = $request->input('image');
        $faceShape = $request->input('face_shape');
        $pref = $request->input('pref', []);
        $userName = $request->input('user_name');
        $userPhone = $request->input('user_phone');
        $hasValidImage = $dataUrl && Str::startsWith($dataUrl, 'data:image');

        // Persist capture (optional for auditing) hanya jika gambar valid
        $storedUrl = null;
        if ($hasValidImage) {
            try {
                [$meta, $content] = explode(',', $dataUrl, 2);
                $binary = base64_decode($content);
                $filename = 'scans/'.date('Ymd_His').'_'.Str::random(6).'.jpg';
                Storage::disk('public')->put($filename, $binary);
                $storedUrl = asset('storage/'.$filename);
            } catch (\Throwable $e) {
                $storedUrl = null;
            }
        }

        // Content-Based Filtering: skor model rambut berdasarkan face_shape + preferensi
        $face = strtolower((string)($faceShape ?: 'oval'));
        $prefLength = strtolower((string)($pref['length'] ?? ''));
        $prefType = strtolower((string)($pref['type'] ?? ''));

        $models = HairModel::all();
        if ($models->isEmpty()) {
            // fallback jika tidak ada data di DB
            $fallback = collect([
                ['name' => 'Oval Layer with Curtain Bangs', 'image' => 'img/model1.png', 'types' => 'Lurus, Ikal, Bergelombang', 'length' => 'Panjang'],
                ['name' => 'Butterfly Hair Cut', 'image' => 'img/model2.png', 'types' => 'Lurus, Ikal, Bergelombang', 'length' => 'Panjang'],
                ['name' => 'Wolf Cut Long Hair', 'image' => 'img/model3.png', 'types' => 'Lurus', 'length' => 'Panjang'],
                ['name' => 'Bob Hair Cut', 'image' => 'img/model2.png', 'types' => 'Lurus', 'length' => 'Pendek'],
                ['name' => 'Pixie Cut', 'image' => 'img/model1.png', 'types' => 'Lurus, Bergelombang', 'length' => 'Pendek'],
                ['name' => 'Face Framing Layers', 'image' => 'img/model3.png', 'types' => 'Lurus', 'length' => 'Panjang'],
                ['name' => 'Wavy Bob', 'image' => 'img/model2.png', 'types' => 'Bergelombang', 'length' => 'Pendek'],
            ]);
            $models = $fallback->map(function ($r) { return (object) $r; });
        }

        // Kata kunci yang cocok per bentuk wajah
        $shapeKeywords = [
            'oval' => ['layer', 'curtain', 'butterfly', 'wolf', 'face framing', 'wavy', 'bob'],
            'bulat' => ['layer', 'curtain', 'butterfly', 'wavy', 'long'],
            'kotak' => ['layer', 'bob', 'soft', 'face framing', 'wavy'],
            'lonjong' => ['bob', 'pixie', 'medium', 'face framing'],
        ];

        $items = $models->map(function ($m) use ($face, $shapeKeywords, $prefLength, $prefType) {
            $score = 0;
            $nameLower = strtolower($m->name ?? '');
            $lengthLower = strtolower($m->length ?? '');
            $typesLower = strtolower((string)($m->types ?? ''));

            // Bentuk wajah: tambahkan skor jika nama mengandung keyword yang direkomendasikan
            $keywords = $shapeKeywords[$face] ?? $shapeKeywords['oval'];
            foreach ($keywords as $kw) {
                if (strpos($nameLower, $kw) !== false) { $score += 2; break; }
            }

            // Preferensi panjang rambut
            if ($prefLength && $lengthLower === strtolower($prefLength)) { $score += 2; }

            // Preferensi jenis rambut
            if ($prefType && strpos($typesLower, strtolower($prefType)) !== false) { $score += 1; }

            // Bentuk wajah "oval" dianggap cocok ke banyak model: bonus kecil
            if ($face === 'oval') { $score += 1; }

            return [
                'name' => $m->name ?? '',
                'image_url' => asset($m->image ?? 'img/model1.png'),
                'score' => $score,
            ];
        })
        ->sortByDesc('score')
        ->take(3)
        ->values();

        // Persist recommendation with safe defaults
        // Kolom 'name' dan 'phone' tidak nullable pada schema saat ini,
        // jadi kita isi default agar data tetap tercatat di laporan meski user tidak mengisi form.
        $saved = false;
        $savedId = null;
        try {
            $rec = Recommendation::create([
                'name' => $userName ?: 'Pengguna',
                'phone' => $userPhone ?: '',
                'hair_length' => $pref['length'] ?? null,
                'hair_type' => $pref['type'] ?? null,
                'hair_condition' => $pref['condition'] ?? null,
                'face_shape' => $faceShape ?: 'oval',
                'recommended_models' => collect($items)->pluck('name')->filter()->implode(', '),
            ]);
            $saved = true;
            $savedId = $rec->id ?? null;
            \Log::info('Recommendation saved', ['id' => $savedId, 'name' => $rec->name, 'models' => $rec->recommended_models]);
        } catch (\Throwable $e) {
            \Log::warning('Recommendation save failed', ['error' => $e->getMessage()]);
            // ignore persist errors for UX
        }

        return response()->json([
            'ok' => true,
            'stored_url' => $storedUrl,
            'face_shape' => $faceShape ?: 'oval',
            'preferences' => $pref,
            'recommendations' => $items,
            'saved' => $saved,
            'saved_id' => $savedId,
        ]);
    }
}