<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\HuggingFaceService;
use Illuminate\Support\Facades\Storage;

echo "🧪 Testing Face Shape Detection\n";
echo "================================\n\n";

// Test 1: Check API Key
$apiKey = config('services.huggingface.api_key');
if ($apiKey) {
    echo "✅ API Key ditemukan: " . substr($apiKey, 0, 15) . "...\n";
} else {
    echo "❌ API Key TIDAK ditemukan!\n";
    echo "   Pastikan HUGGINGFACE_API_KEY sudah ditambahkan di .env\n";
    exit(1);
}

// Test 2: Check if there are any stored images
echo "\n📁 Checking for stored images...\n";
$scanFiles = Storage::disk('public')->files('scans');
if (count($scanFiles) > 0) {
    echo "   ✅ Found " . count($scanFiles) . " image(s) in storage/scans\n";
    $testImage = $scanFiles[0];
    echo "   📷 Testing with: " . $testImage . "\n";
    
    // Test 3: Test face shape detection
    echo "\n🔍 Testing face shape detection...\n";
    try {
        $service = new HuggingFaceService();
        
        // Test with storage path
        echo "   Method 1: Storage path\n";
        $result1 = $service->analyzeFaceShape($testImage);
        if ($result1) {
            echo "   ✅ Success! Face shape: " . $result1['label'] . " (confidence: " . ($result1['score'] ?? 0) . ")\n";
        } else {
            echo "   ❌ Failed\n";
        }
        
        // Test with full URL
        $fullUrl = asset('storage/' . $testImage);
        echo "\n   Method 2: Full URL\n";
        echo "   URL: " . $fullUrl . "\n";
        $result2 = $service->analyzeFaceShape($fullUrl);
        if ($result2) {
            echo "   ✅ Success! Face shape: " . $result2['label'] . " (confidence: " . ($result2['score'] ?? 0) . ")\n";
        } else {
            echo "   ❌ Failed\n";
        }
        
    } catch (\Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
        echo "   📝 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
} else {
    echo "   ⚠️  No images found in storage/scans\n";
    echo "   💡 Upload a photo first through the web interface\n";
}

// Test 4: Test comprehensive analysis
echo "\n🔍 Testing comprehensive analysis...\n";
if (count($scanFiles) > 0) {
    try {
        $service = new HuggingFaceService();
        $preferences = [
            'length' => 'Panjang',
            'type' => 'Lurus',
            'condition' => 'Normal'
        ];
        
        $testImage = $scanFiles[0];
        $fullUrl = asset('storage/' . $testImage);
        
        echo "   Testing with URL: " . substr($fullUrl, 0, 80) . "...\n";
        $result = $service->comprehensiveAnalysis($fullUrl, $preferences);
        
        if ($result) {
            echo "   ✅ Comprehensive analysis completed!\n";
            echo "   📊 Results:\n";
            echo "      - Face Shape: " . ($result['face_shape'] ?? 'N/A') . "\n";
            echo "      - Confidence: " . ($result['face_shape_confidence'] ?? 0) . "\n";
            echo "      - Hair Type: " . ($result['hair_type'] ?? 'N/A') . "\n";
            echo "      - Hair Length: " . ($result['hair_length'] ?? 'N/A') . "\n";
            echo "      - Method: " . ($result['analysis_method'] ?? 'N/A') . "\n";
        } else {
            echo "   ❌ Comprehensive analysis failed\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
    }
}

echo "\n✅ Test selesai!\n";
echo "\n💡 Tips:\n";
echo "   - Jika semua test gagal, cek koneksi internet\n";
echo "   - Model pertama kali mungkin perlu loading (503 error)\n";
echo "   - Tunggu 30-60 detik dan coba lagi\n";
echo "   - Cek storage/logs/laravel.log untuk detail error\n";


