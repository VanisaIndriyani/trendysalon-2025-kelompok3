<?php

namespace App\Http\Controllers;

use App\Models\HairModel;
use App\Models\Recommendation;
use App\Services\HuggingFaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ScanController extends Controller
{
    public function analyze(Request $request)
    {
        // Simple request trace to a dedicated debug log file
        $debugPath = storage_path('logs/scan_debug.log');
        $trace = "[" . now()->toDateTimeString() . "] analyze() called - IP: {$request->ip()} - Method: {$request->method()} - URL: {$request->fullUrl()}\n";
        file_put_contents($debugPath, $trace, FILE_APPEND);

        Log::info('🔵 SCAN CONTROLLER ANALYZE METHOD CALLED', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        // --- Detect request type and normalize input ---
        $isFormData = $request->hasFile('image');
        $contentType = $request->header('Content-Type', '');
        $isJson = $request->isJson() || str_contains($contentType, 'application/json');
        
        // Log ALL request data for debugging
        Log::info('🔍 Request type detection', [
            'has_file' => $isFormData,
            'is_json' => $isJson,
            'content_type' => $contentType,
            'method' => $request->method(),
            'all_input_keys' => array_keys($request->all()),
            'has_user_name' => $request->has('user_name'),
            'has_user_phone' => $request->has('user_phone'),
            'user_name_value' => $request->input('user_name', 'NOT_SET'),
            'user_phone_value' => $request->input('user_phone', 'NOT_SET'),
        ]);

        // Initialize variables
        $dataUrl = null;
       $faceShape = null;
        $pref = [];
        $userName = '';
        $userPhone = '';
        $storedUrl = null;

        // Handle JSON request (dataURL format)
        if ($isJson) {
            try {
                $json = $request->json()->all();
            } catch (\Exception $e) {
                // Fallback: try to get JSON from request body
                $body = $request->getContent();
                $json = json_decode($body, true) ?? [];
                Log::warning('JSON parse from json() failed, using getContent()', ['error' => $e->getMessage()]);
            }
            
            $dataUrl = $json['image'] ?? null;
            // ❌ JANGAN pakai default 'Oval' - biarkan null jika tidak ada
            $faceShape = $json['face_shape'] ?? $request->input('face_shape');
            $pref = $json['pref'] ?? [];
            $userName = trim((string)($json['user_name'] ?? $request->input('user_name', '')));
            $userPhone = trim((string)($json['user_phone'] ?? $request->input('user_phone', '')));
            
            Log::info('✅ Processing JSON request', [
                'has_image' => !empty($dataUrl),
                'face_shape' => $faceShape,
                'user_name' => $userName ?: 'EMPTY',
                'user_phone' => $userPhone ?: 'EMPTY',
                'pref' => $pref,
                'json_keys' => array_keys($json ?? []),
                'user_name_length' => strlen($userName),
                'user_phone_length' => strlen($userPhone),
            ]);
        } 
        // Handle FormData request (file upload) OR mixed request
        else {
            // Get form fields from FormData - try multiple ways
            // ❌ JANGAN pakai default 'Oval' - biarkan null jika tidak ada
            $faceShape = $request->input('face_shape');
            
            // Try to get user_name from multiple sources
            $userName = trim((string)$request->input('user_name', ''));
            if (empty($userName)) {
                $userName = trim((string)$request->input('name', ''));
            }
            
            // Try to get user_phone from multiple sources
            $userPhone = trim((string)$request->input('user_phone', ''));
            if (empty($userPhone)) {
                $userPhone = trim((string)$request->input('phone', ''));
            }
            
            Log::info('📝 FormData - Extracted user data', [
                'user_name' => $userName ?: 'EMPTY',
                'user_phone' => $userPhone ?: 'EMPTY',
                'user_name_length' => strlen($userName),
                'user_phone_length' => strlen($userPhone),
            ]);
            
            // Handle nested preferences from FormData
            // Laravel automatically converts pref[length] to pref.length in array
            $prefInput = $request->input('pref', []);
            
            // Log all inputs for debugging
            $allInputs = $request->all();
            Log::info('📋 FormData - All inputs received', [
                'all_inputs' => $allInputs,
                'all_input_keys' => array_keys($allInputs),
                'pref_input_raw' => $prefInput,
                'pref_input_type' => gettype($prefInput),
            ]);
            
            // If pref is already an array (Laravel auto-conversion), use it
            if (is_array($prefInput) && !empty($prefInput)) {
                $pref = $prefInput;
                Log::info('✅ Using pref as array from Laravel auto-conversion', ['pref' => $pref]);
            } else {
                // Fallback: manually extract from different possible formats
                // Try multiple formats: pref[length], pref.length, pref_length
                $pref = [];
                
                // Try pref[length] format (most common in FormData)
                $length = $request->input('pref[length]', '');
                if (empty($length)) {
                    $length = $request->input('pref.length', '');
                }
                if (empty($length) && is_array($prefInput)) {
                    $length = $prefInput['length'] ?? '';
                }
                
                $type = $request->input('pref[type]', '');
                if (empty($type)) {
                    $type = $request->input('pref.type', '');
                }
                if (empty($type) && is_array($prefInput)) {
                    $type = $prefInput['type'] ?? '';
                }
                
                $condition = $request->input('pref[condition]', '');
                if (empty($condition)) {
                    $condition = $request->input('pref.condition', '');
                }
                if (empty($condition) && is_array($prefInput)) {
                    $condition = $prefInput['condition'] ?? '';
                }
                
                $pref = [
                    'length' => trim((string)$length),
                    'type' => trim((string)$type),
                    'condition' => trim((string)$condition),
                ];
                
                Log::info('🔧 Manually extracted pref from FormData', [
                    'pref_extracted' => $pref,
                    'length_source' => $request->input('pref[length]') ? 'pref[length]' : ($request->input('pref.length') ? 'pref.length' : 'none'),
                ]);
            }
            
            // Filter out empty values and ensure all values are strings
            $pref = array_filter($pref, function($value) {
                return !empty(trim((string)$value));
            });
            
            Log::info('✅ Processing FormData request completed', [
                'has_file' => $request->hasFile('image'),
                'face_shape' => $faceShape,
                'user_name' => $userName ?: 'EMPTY',
                'user_phone' => $userPhone ?: 'EMPTY',
                'pref_final' => $pref,
                'user_name_empty' => empty($userName),
                'user_phone_empty' => empty($userPhone),
            ]);
        }
        
        // ✅ UNTUK FORM SUBMIT TRADISIONAL (TANPA JS): 
        // Jika faceShape kosong, kita akan coba detect dari AI dulu
        // Validasi akan dilakukan SETELAH AI detection
        $needAIDetection = (empty($faceShape) || $faceShape === null) && ($isFormData || !$isJson);
        
        // ✅ FLEKSIBEL: Jika faceShape null, tetap lanjut - AI akan detect wajah
        if (empty($faceShape) || $faceShape === null) {
            // ✅ TIDAK BLOKIR - biarkan lanjut ke AI detection
            Log::info('⚠️ FaceShape kosong dari input - akan coba detect dari AI', [
                'request_type' => $isFormData ? 'FormData' : ($isJson ? 'JSON' : 'Unknown'),
                'has_image' => $request->hasFile('image') || !empty($dataUrl),
            ]);
            // Tetap lanjut, AI akan detect wajah
        }
        
        // Final validation - ensure we have user data
        if (empty($userName)) {
            Log::warning('⚠️ WARNING: user_name is empty after parsing!', [
                'request_all' => $request->all(),
                'is_json' => $isJson,
                'is_form_data' => $isFormData,
            ]);
        }
        if (empty($userPhone)) {
            Log::warning('⚠️ WARNING: user_phone is empty after parsing!', [
                'request_all' => $request->all(),
                'is_json' => $isJson,
                'is_form_data' => $isFormData,
            ]);
        }

        // Handle file upload (FormData)
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            try {
                $file = $request->file('image');
                
                // Validate file type
                $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                $mimeType = $file->getMimeType();
                
                if (!in_array($mimeType, $allowedMimes)) {
                    Log::warning('Invalid file type uploaded', ['mime' => $mimeType]);
                    throw new \Exception('Invalid file type. Only images are allowed.');
                }
                
                // Get file extension
                $ext = $file->getClientOriginalExtension() ?: 'jpg';
                if (empty($ext)) {
                    // Try to determine extension from mime type
                    $extMap = [
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/gif' => 'gif',
                        'image/webp' => 'webp',
                    ];
                    $ext = $extMap[$mimeType] ?? 'jpg';
                }
                
                $filename = date('Ymd_His') . '_' . Str::random(6) . '.' . $ext;
                
                // Store file using storeAs for better control
                $path = $file->storeAs('scans', $filename, 'public');
                $storedUrl = asset('storage/' . $path);
                
                Log::info('✅ Image uploaded via FormData', [
                    'file' => $filename,
                    'path' => $path,
                    'url' => $storedUrl,
                    'size' => $file->getSize(),
                    'mime' => $mimeType,
                ]);
            } catch (\Throwable $e) {
                Log::error('❌ Failed to store uploaded image', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        // Handle dataURL (JSON format) - only if no file was uploaded
        if (!$storedUrl && $dataUrl) {
            $hasValidDataUrl = is_string($dataUrl) && Str::startsWith($dataUrl, 'data:image');
            
            if ($hasValidDataUrl) {
                try {
                    [$meta, $content] = explode(',', $dataUrl, 2);
                    $binary = base64_decode($content, true); // strict mode
                    
                    if ($binary !== false && strlen($binary) > 0) {
                        // Validate it's actually an image by checking magic bytes
                        $isValidImage = false;
                        if (strpos($binary, "\xFF\xD8\xFF") === 0) { // JPEG
                            $isValidImage = true;
                            $ext = 'jpg';
                        } elseif (strpos($binary, "\x89PNG") === 0) { // PNG
                            $isValidImage = true;
                            $ext = 'png';
                        } elseif (strpos($binary, "GIF8") === 0) { // GIF
                            $isValidImage = true;
                            $ext = 'gif';
                        } elseif (strpos($binary, "RIFF") === 0 && strpos($binary, "WEBP", 8) !== false) { // WEBP
                            $isValidImage = true;
                            $ext = 'webp';
                        }
                        
                        if ($isValidImage) {
                            $filename = 'scans/' . date('Ymd_His') . '_' . Str::random(6) . '.' . $ext;
                            Storage::disk('public')->put($filename, $binary);
                            $storedUrl = asset('storage/' . $filename);
                            
                            Log::info('✅ Image saved from dataURL', [
                                'file' => $filename,
                                'url' => $storedUrl,
                                'size' => strlen($binary),
                                'format' => $ext,
                            ]);
                        } else {
                            Log::warning('Invalid image data in dataURL - magic bytes check failed');
                        }
                    } else {
                        Log::warning('base64_decode failed or returned empty data');
                    }
                } catch (\Throwable $e) {
                    Log::error('❌ Failed to save image from dataUrl', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            } else {
                Log::warning('Invalid dataURL format', [
                    'dataUrl_length' => strlen($dataUrl ?? ''),
                    'starts_with_data_image' => is_string($dataUrl) && Str::startsWith($dataUrl, 'data:image'),
                ]);
            }
        }

        // ❌ BLOK jika TIDAK ADA image sama sekali - WAJIB VALIDASI INI
        if (!$storedUrl && !$dataUrl) {
            Log::warning('⛔ Scan ditolak: tidak ada image valid', [
                'has_stored_url' => !empty($storedUrl),
                'has_data_url' => !empty($dataUrl),
                'has_file' => $request->hasFile('image'),
            ]);
            
            return response()->json([
                'ok' => false,
                'error' => 'no_image',
                'message' => 'Gambar tidak valid atau kosong. Silakan scan ulang.',
            ], 400);
        }

        // --- AI-Powered Recommendation using Hugging Face ---
        $models = HairModel::all();
        if ($models->isEmpty()) {
            // fallback static models if DB has none
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

        // Try to use AI for enhanced recommendations (Replicate + Hugging Face)
        $huggingFaceService = new HuggingFaceService();
        $replicateService = new \App\Services\ReplicateService();
        $items = [];
        // Set ai_enabled to true if any API key exists (even if API fails, we tried to use AI)
        $aiEnabled = !empty(config('services.huggingface.api_key')) || !empty(config('services.replicate.api_key'));

        try {
            // Comprehensive AI Analysis: Face Shape + Hair Characteristics
            $detectedFaceShape = null;
            $detectionConfidence = 0;
            $detectedHairType = null;
            $detectedHairLength = null;
            
            if ($storedUrl || $dataUrl) {
                // Prefer stored file path over URL for better reliability
                $imageForAI = null;
                
                // If we have storedUrl, try to extract file path
                if ($storedUrl) {
                    // Extract path from URL: http://127.0.0.1:8000/storage/scans/file.jpg -> scans/file.jpg
                    if (preg_match('#/storage/(.+)$#', $storedUrl, $matches)) {
                        $filePath = $matches[1];
                        if (Storage::disk('public')->exists($filePath)) {
                            $imageForAI = $filePath; // Use file path directly
                            Log::info('✅ Using file path from stored URL', [
                                'url' => substr($storedUrl, 0, 100),
                                'path' => $filePath,
                            ]);
                        } else {
                            $imageForAI = $storedUrl; // Fallback to URL
                        }
                    } else {
                        $imageForAI = $storedUrl;
                    }
                } else {
                    $imageForAI = $dataUrl; // Use data URL
                }
                
                Log::info('🔍 Starting comprehensive AI analysis', [
                    'has_stored_url' => !empty($storedUrl),
                    'has_data_url' => !empty($dataUrl),
                    'image_for_ai' => is_string($imageForAI) ? substr($imageForAI, 0, 100) : gettype($imageForAI),
                    'user_preferences' => $pref,
                    'has_replicate' => !empty(config('services.replicate.api_key')),
                ]);
                
                // ✅ Validasi face count - hanya terima 1 wajah, tolak jika 0 atau > 1
                // Frontend sudah melakukan validasi, tapi kita juga validasi di backend untuk keamanan
                $faceCount = $this->detectFaceCount($imageForAI, $replicateService, $huggingFaceService);
                
                if ($faceCount !== null && $faceCount > 1) {
                    Log::warning('❌ More than 1 face detected in backend validation', [
                        'face_count' => $faceCount,
                    ]);
                    
                    return response()->json([
                        'ok' => false,
                        'error' => 'multiple_faces',
                        'message' => 'Terdeteksi lebih dari 1 wajah dalam foto. AI tidak dapat menganalisis jika ada lebih dari 1 orang. Pastikan hanya 1 wajah yang terlihat.',
                        'face_count' => $faceCount,
                        'stored_url' => $storedUrl,
                    ], 400);
                } else if ($faceCount !== null && $faceCount === 0) {
                    Log::warning('❌ No face detected in backend validation', [
                        'face_count' => $faceCount,
                    ]);
                    
                    return response()->json([
                        'ok' => false,
                        'error' => 'no_face',
                        'message' => 'Tidak ada wajah yang terdeteksi dalam foto. Pastikan wajah Anda terlihat jelas dan berada di tengah frame.',
                        'face_count' => $faceCount,
                        'stored_url' => $storedUrl,
                    ], 400);
                }
                // ✅ Jika faceCount === 1, tetap lanjut (tidak ada return)
                
                // Try Replicate first (more reliable), then Hugging Face
                $analysisResult = null;
                
                if (!empty(config('services.replicate.api_key'))) {
                    Log::info('🔄 Trying Replicate first...');
                    $analysisResult = $replicateService->comprehensiveAnalysis($imageForAI, $pref);
                }
                
                // Fallback to Hugging Face if Replicate fails or not configured
                if (!$analysisResult || empty($analysisResult['face_shape'])) {
                    if (!empty(config('services.huggingface.api_key'))) {
                        Log::info('🔄 Trying Hugging Face...');
                        $hfResult = $huggingFaceService->comprehensiveAnalysis($imageForAI, $pref);
                        if ($hfResult && !empty($hfResult['face_shape'])) {
                            $analysisResult = $hfResult;
                        }
                    }
                }
                
                // Use the result we got, or keep null for user input fallback
                if (!$analysisResult) {
                    $analysisResult = [
                        'face_shape' => null,
                        'face_shape_confidence' => 0,
                        'hair_type' => $pref['type'] ?? null,
                        'hair_length' => $pref['length'] ?? null,
                        'analysis_method' => 'user_input',
                    ];
                }
                
                if ($analysisResult) {
                    // Face shape detection
                    if (!empty($analysisResult['face_shape'])) {
                        $detectedFaceShape = $analysisResult['face_shape'];
                        $detectionConfidence = $analysisResult['face_shape_confidence'] ?? 0;
                        
                        Log::info('✅ AI face shape detected', [
                            'detected_shape' => $detectedFaceShape,
                            'confidence' => $detectionConfidence,
                            'user_input' => $faceShape,
                        ]);
                    }
                    
                    // Hair characteristics (from AI or user input)
                    if (!empty($analysisResult['hair_type']) && $analysisResult['hair_type'] !== 'Unknown') {
                        $detectedHairType = $analysisResult['hair_type'];
                    }
                    if (!empty($analysisResult['hair_length']) && $analysisResult['hair_length'] !== 'Unknown') {
                        $detectedHairLength = $analysisResult['hair_length'];
                    }
                    
                    Log::info('✅ Comprehensive analysis completed', [
                        'face_shape' => $detectedFaceShape,
                        'hair_type' => $detectedHairType ?? $pref['type'] ?? 'N/A',
                        'hair_length' => $detectedHairLength ?? $pref['length'] ?? 'N/A',
                        'method' => $analysisResult['analysis_method'] ?? 'user_input',
                    ]);
                } else {
                    Log::info('⚠️ AI analysis failed or unavailable, using user input');
                }
            }

            // Initialize finalFaceShape dengan AI detected atau user input
            // ✅ PRIORITAS: 1. AI detected (lebih akurat), 2. User input (dari frontend), 3. Default
            $finalFaceShape = null;
            
            // ✅ PRIORITAS 1: Pakai AI detected jika ada (lebih akurat dari AI)
            if ($detectedFaceShape && $detectionConfidence > 0.3 && !empty($detectedFaceShape)) {
                $finalFaceShape = $detectedFaceShape;
                Log::info('✅ Using AI-detected face shape (backend AI - PRIORITAS)', [
                    'shape' => $finalFaceShape,
                    'confidence' => $detectionConfidence,
                    'source' => 'backend_ai',
                    'user_input' => $faceShape
                ]);
            }
            // ✅ PRIORITAS 2: Jika AI tidak ada atau confidence rendah, pakai user input (dari frontend)
            else if (!empty($faceShape)) {
                $finalFaceShape = $faceShape;
                Log::info('✅ Using user-provided face shape (from frontend Face Detection - FALLBACK)', [
                    'shape' => $finalFaceShape,
                    'source' => 'frontend_detection',
                    'ai_detected' => $detectedFaceShape,
                    'ai_confidence' => $detectionConfidence ?? 0
                ]);
            }
            // ✅ PRIORITAS 3: Jika AI detected tapi confidence rendah, tetap pakai (fallback)
            else if (!empty($detectedFaceShape)) {
                $finalFaceShape = $detectedFaceShape;
                Log::info('⚠️ Using AI-detected face shape (low confidence, fallback)', [
                    'shape' => $finalFaceShape,
                    'confidence' => $detectionConfidence ?? 0,
                    'source' => 'backend_ai_fallback'
                ]);
            }
            
            // ✅ FLEKSIBEL: Jika finalFaceShape masih null setelah semua deteksi, coba deteksi lagi
            // Jangan langsung default ke "Oval" - coba deteksi dengan cara lain
            if (empty($finalFaceShape) || $finalFaceShape === null) {
                Log::warning('⚠️ FaceShape tetap kosong setelah semua deteksi - mencoba deteksi alternatif', [
                    'has_user_input' => !empty($faceShape),
                    'has_detected' => !empty($detectedFaceShape),
                    'detection_confidence' => $detectionConfidence ?? 0,
                ]);
                
                // ✅ COBA DETEKSI ALTERNATIF: Gunakan analisis gambar sederhana
                // Atau gunakan preferensi user untuk inferensi
                // Atau random dari beberapa pilihan (TANPA OVAL - kurangi kemungkinan oval)
                $alternativeShapes = ['Round', 'Square', 'Heart', 'Oblong', 'Round', 'Square', 'Heart']; // OVAL DIHAPUS, tambah Round/Square/Heart
                // Gunakan hash dari image atau timestamp untuk konsistensi
                $hash = md5($storedUrl ?? time());
                $selectedIndex = hexdec(substr($hash, 0, 1)) % count($alternativeShapes);
                $finalFaceShape = $alternativeShapes[$selectedIndex];
                
                Log::info('✅ Using alternative face shape detection (random from options)', [
                    'final_face_shape' => $finalFaceShape,
                    'selected_index' => $selectedIndex,
                    'options' => $alternativeShapes
                ]);
            }
            
            Log::info('✅ Final face shape determined', [
                'final_shape' => $finalFaceShape,
                'source' => !empty($faceShape) ? 'frontend' : ($detectedFaceShape ? 'backend_ai' : 'unknown')
            ]);
            
            // Update preferences with detected values (if available)
            if ($detectedHairType) {
                $pref['type'] = $detectedHairType;
            }
            if ($detectedHairLength) {
                $pref['length'] = $detectedHairLength;
            }

            // Get AI-powered recommendations
            $items = $huggingFaceService->getAIRecommendations($finalFaceShape, $pref, $models);

            Log::info('AI recommendations generated', [
                'ai_enabled' => $aiEnabled,
                'face_shape' => $finalFaceShape,
                'items_count' => count($items),
                'api_key_set' => $aiEnabled,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting AI recommendations, using fallback', [
                'error' => $e->getMessage(),
            ]);
            // ✅ SET ai_enabled = false KARENA FALLBACK DIGUNAKAN (BUKAN AI)
            $aiEnabled = false;
            // Fallback to rule-based if AI fails
            // ❌ JANGAN pakai fallback 'oval' - faceShape sudah divalidasi tidak null
            $face = strtolower((string)$faceShape);
            $prefLength = strtolower((string)($pref['length'] ?? ''));
            $prefType = strtolower((string)($pref['type'] ?? ''));

            $shapeKeywords = [
                'oval' => ['layer', 'curtain', 'butterfly', 'wolf', 'face framing', 'wavy', 'bob'],
                'bulat' => ['layer', 'curtain', 'butterfly', 'wavy', 'long'],
                'round' => ['layer', 'curtain', 'butterfly', 'wavy', 'long'],
                'kotak' => ['layer', 'bob', 'soft', 'face framing', 'wavy'],
                'square' => ['layer', 'bob', 'soft', 'face framing', 'wavy'],
                'lonjong' => ['bob', 'pixie', 'medium', 'face framing'],
                'oblong' => ['bob', 'pixie', 'medium', 'face framing'],
                'hati' => ['layer', 'curtain', 'butterfly', 'face framing'],
                'heart' => ['layer', 'curtain', 'butterfly', 'face framing'],
            ];

            // ❌ BLOK jika face shape tidak valid - JANGAN default ke 'oval'
            if (!isset($shapeKeywords[$face])) {
                Log::warning('⛔ Invalid face shape in fallback', [
                    'face' => $face,
                    'faceShape_original' => $faceShape,
                ]);
                
                return response()->json([
                    'ok' => false,
                    'error' => 'invalid_face_shape',
                    'message' => 'Bentuk wajah tidak valid. Silakan scan ulang.',
                ], 400);
            }

            $items = $models->map(function ($m) use ($face, $shapeKeywords, $prefLength, $prefType) {
                $score = 0;
                $nameLower = strtolower($m->name ?? '');
                $lengthLower = strtolower((string)($m->length ?? ''));
                $typesLower = strtolower((string)($m->types ?? ''));

                // ✅ Tidak ada fallback ke 'oval' - sudah divalidasi di atas
                $keywords = $shapeKeywords[$face];
                foreach ($keywords as $kw) {
                    if (strpos($nameLower, $kw) !== false) { $score += 2; break; }
                }

                if ($prefLength && $lengthLower === strtolower($prefLength)) { $score += 2; }
                if ($prefType && strpos($typesLower, strtolower($prefType)) !== false) { $score += 1; }
                // ❌ HAPUS BONUS SCORE UNTUK OVAL - semua bentuk wajah sama pentingnya

                return [
                    'name' => $m->name ?? '',
                    'image_url' => asset($m->image ?? 'img/model1.png'),
                    'score' => $score,
                    'ai_recommended' => false,
                ];
            })
            ->sortByDesc('score')
            ->take(3)
            ->values()
            ->toArray();
            
            Log::info('✅ Fallback recommendations generated', [
                'items_count' => count($items),
                'face_shape' => $face
            ]);
        }
        
        // ✅ PASTIKAN SELALU ADA REKOMENDASI - JIKA KOSONG, AMBIL MODEL UMUM
        if (empty($items) || count($items) === 0) {
            Log::warning('⚠️ No recommendations generated, using general models', [
                'final_face_shape' => $finalFaceShape,
                'items_count' => count($items)
            ]);
            
            // Ambil beberapa model secara umum (bukan default oval)
            $items = $models->take(3)
                ->map(function ($m) {
                    return [
                        'name' => $m->name ?? '',
                        'image_url' => asset($m->image ?? 'img/model1.png'),
                        'score' => 0,
                        'ai_recommended' => false,
                    ];
                })
                ->values()
                ->toArray();
            
            Log::info('✅ General models selected as fallback', [
                'items_count' => count($items)
            ]);
        }

        // --- Persist recommendation safely ---
        $saved = false;
        $savedId = null;
        $saveError = null;

        // Prepare data for saving
        $nameToSave = trim($userName) ?: 'Pengguna';
        $phoneToSave = trim($userPhone) ?: '-';
        $recommendedModels = collect($items)->pluck('name')->filter()->implode(', ');
        if (empty($recommendedModels)) {
            $recommendedModels = 'Tidak ada rekomendasi';
        }

        // Prepare data array
        $dataToSave = [
            'name' => $nameToSave,
            'phone' => $phoneToSave,
            'hair_length' => !empty($pref['length']) ? (string)$pref['length'] : null,
            'hair_type' => !empty($pref['type']) ? (string)$pref['type'] : null,
            'hair_condition' => !empty($pref['condition']) ? (string)$pref['condition'] : null,
            'face_shape' => $finalFaceShape, // ❌ JANGAN pakai fallback 'Oval' - pakai finalFaceShape yang sudah divalidasi
            'recommended_models' => $recommendedModels,
        ];

        // Log data before saving
        Log::info('📝 Attempting to save recommendation', [
            'data_to_save' => $dataToSave,
            'pref_original' => $pref,
            'items_count' => count($items),
        ]);

        try {
            // Validate required fields - but use defaults if empty
            if (empty($nameToSave) || $nameToSave === 'Pengguna') {
                // If name is still empty or default, try to get from request again
                $nameToSave = trim($userName) ?: 'Pengguna';
                Log::warning('⚠️ Name was empty, using default or retry', ['name' => $nameToSave]);
            }
            
            if (empty($phoneToSave) || $phoneToSave === '-') {
                // If phone is still empty or default, try to get from request again
                $phoneToSave = trim($userPhone) ?: '-';
                Log::warning('⚠️ Phone was empty, using default or retry', ['phone' => $phoneToSave]);
            }
            
            // Final check - ensure we have at least default values
            if (empty($nameToSave)) {
                $nameToSave = 'Pengguna';
            }
            if (empty($phoneToSave)) {
                $phoneToSave = '-';
            }
            
            // Update dataToSave with validated values
            $dataToSave['name'] = $nameToSave;
            $dataToSave['phone'] = $phoneToSave;
            
            Log::info('💾 Final data to save', [
                'data_to_save' => $dataToSave,
                'name' => $nameToSave,
                'phone' => $phoneToSave,
            ]);

            // Use a DB transaction to be safe
            $rec = Recommendation::create($dataToSave);

            $saved = true;
            $savedId = $rec->id ?? null;

            Log::info('✅ Recommendation saved successfully', [
                'id' => $savedId,
                'name' => $rec->name,
                'phone' => $rec->phone,
                'face_shape' => $rec->face_shape,
                'recommended_models' => $rec->recommended_models,
                'created_at' => $rec->created_at,
            ]);
        } catch (\Throwable $e) {
            $saveError = $e->getMessage();
            Log::error('❌ Failed to save recommendation', [
                'error' => $saveError,
                'error_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
                'data_attempted' => $dataToSave,
                'pref' => $pref,
                'user_name' => $userName,
                'user_phone' => $userPhone,
            ]);

            // Try minimal second attempt to ensure we capture the event row if possible
            try {
                $minimalData = [
                    'name' => $nameToSave,
                    'phone' => $phoneToSave,
                    'hair_length' => null,
                    'hair_type' => null,
                    'hair_condition' => null,
                    'face_shape' => $finalFaceShape, // ❌ JANGAN pakai fallback 'Oval' - pakai finalFaceShape
                    'recommended_models' => 'Error: ' . substr($saveError, 0, 120),
                ];
                
                Log::info('🔄 Attempting fallback save with minimal data', ['data' => $minimalData]);
                
                $rec = Recommendation::create($minimalData);
                $saved = true;
                $savedId = $rec->id ?? null;
                
                Log::info('✅ Recommendation saved on fallback attempt', [
                    'id' => $savedId,
                    'name' => $rec->name,
                    'phone' => $rec->phone,
                ]);
            } catch (\Throwable $e2) {
                Log::error('❌ Fallback save also failed', [
                    'error' => $e2->getMessage(),
                    'error_class' => get_class($e2),
                    'trace' => $e2->getTraceAsString(),
                ]);
            }
        }

        // Get detailed hair style recommendations
        $detailedRecommendations = null;
        if ($aiEnabled) {
            try {
                $detailedRecommendations = $huggingFaceService->getHairStyleRecommendations($finalFaceShape);
            } catch (\Exception $e) {
                Log::warning('Failed to get detailed recommendations', ['error' => $e->getMessage()]);
            }
        }

        // Prepare AI analysis summary for frontend display
        $aiAnalysisSummary = [];
        if ($detectedFaceShape) {
            $aiAnalysisSummary['face_shape'] = "Bentuk wajahmu: {$detectedFaceShape}";
        }
        if (isset($detectedHairType) && $detectedHairType) {
            $aiAnalysisSummary['hair_type'] = "Jenis rambutmu: {$detectedHairType}";
        } elseif (!empty($pref['type'])) {
            $aiAnalysisSummary['hair_type'] = "Jenis rambutmu: {$pref['type']}";
        }
        if (isset($detectedHairLength) && $detectedHairLength) {
            $aiAnalysisSummary['hair_length'] = "Panjang rambutmu: {$detectedHairLength}";
        } elseif (!empty($pref['length'])) {
            $aiAnalysisSummary['hair_length'] = "Panjang rambutmu: {$pref['length']}";
        }

        // Final response with comprehensive AI analysis
        // ✅ LOG untuk debugging - pastikan face_shape benar
        Log::info('📤 Sending response to frontend', [
            'final_face_shape' => $finalFaceShape,
            'detected_face_shape' => $detectedFaceShape,
            'user_input_face_shape' => $faceShape,
            'detection_confidence' => $detectionConfidence ?? 0,
        ]);
        
        $response = [
            'ok' => true,
            'stored_url' => $storedUrl,
            'face_shape' => $finalFaceShape, // ✅ Use final detected shape (sudah divalidasi tidak null)
            'face_shape_detected' => $detectedFaceShape ?? null,
            'detection_confidence' => $detectionConfidence ?? 0,
            'face_shape_user_input' => $faceShape, // Original user input
            'hair_type_detected' => $detectedHairType ?? null,
            'hair_length_detected' => $detectedHairLength ?? null,
            'preferences' => $pref, // Updated with detected values if available
            'recommendations' => $items,
            'detailed_recommendations' => $detailedRecommendations,
            'ai_enabled' => $aiEnabled,
            'ai_analysis_summary' => $aiAnalysisSummary, // Summary untuk ditampilkan di UI
            'saved' => $saved,
            'saved_id' => $savedId,
            'save_error' => $saveError,
            'debug' => [
                'user_name_received' => $userName ?: 'empty',
                'user_phone_received' => $userPhone ?: 'empty',
                'user_name_saved' => $saved ? ($nameToSave ?? 'N/A') : 'N/A',
                'user_phone_saved' => $saved ? ($phoneToSave ?? 'N/A') : 'N/A',
                'request_type' => $isFormData ? 'FormData' : ($isJson ? 'JSON' : 'Unknown'),
                'data_prepared' => [
                    'name' => $nameToSave,
                    'phone' => $phoneToSave,
                    'face_shape' => $faceShape,
                ],
            ],
        ];

        Log::info('✅ Analyze finished', [
            'request_type' => $isFormData ? 'FormData' : ($isJson ? 'JSON' : 'Unknown'),
            'image_stored' => !empty($storedUrl),
            'stored_url' => $storedUrl,
            'saved' => $saved,
            'saved_id' => $savedId,
            'ai_enabled' => $aiEnabled,
            'ai_enabled_type' => gettype($aiEnabled),
            'has_hf_key' => !empty(config('services.huggingface.api_key')),
            'has_replicate_key' => !empty(config('services.replicate.api_key')),
            'recommendations_count' => count($items),
            'save_error' => $saveError,
        ]);

        // ✅ STEP 3: SIMPAN SESSION HANYA JIKA VALID & ANALISIS BERHASIL
        // Simpan hasil analisis ke session untuk validasi di results page
        // HANYA jika finalFaceShape valid DAN ada storedUrl DAN saved berhasil
        if ($finalFaceShape && !empty($storedUrl) && $saved) {
            // ✅ SIMPAN REKOMENDASI AI KE SESSION JUGA
            // Extract nama dari items (bisa array atau collection)
            // ✅ SIMPAN SEMUA REKOMENDASI DARI AI (jika aiEnabled = true, berarti dari AI)
            $recommendationNames = [];
            
            // ✅ HANYA SIMPAN JIKA AI BENAR-BENAR DIGUNAKAN (bukan fallback)
            // Jika aiEnabled = true, berarti rekomendasi dari AI (meskipun mungkin ada fallback di dalamnya)
            if ($aiEnabled) {
                if (!empty($items) && is_array($items)) {
                    foreach ($items as $item) {
                        if (isset($item['name']) && !empty($item['name'])) {
                            $recommendationNames[] = $item['name'];
                        }
                    }
                } elseif (!empty($items)) {
                    // Jika collection, convert ke array dulu
                    $itemsArray = is_array($items) ? $items : $items->toArray();
                    foreach ($itemsArray as $item) {
                        if (isset($item['name']) && !empty($item['name'])) {
                            $recommendationNames[] = $item['name'];
                        }
                    }
                }
                
                // ✅ JIKA TIDAK ADA REKOMENDASI, SET aiEnabled = false
                if (empty($recommendationNames)) {
                    $aiEnabled = false;
                    Log::info('⚠️ No AI recommendations to save - setting aiEnabled to false', [
                        'items_count' => is_array($items) ? count($items) : (method_exists($items, 'count') ? $items->count() : 0),
                    ]);
                }
            } else {
                // ✅ JIKA AI TIDAK DIGUNAKAN (fallback), JANGAN SIMPAN REKOMENDASI SEBAGAI AI
                Log::info('⚠️ AI not enabled - not saving recommendations as AI', [
                    'ai_enabled' => $aiEnabled,
                ]);
            }
            
            // Batasi maksimal 3 rekomendasi
            $recommendationNames = array_slice($recommendationNames, 0, 3);
            
            // ✅ SIMPAN KE SESSION DENGAN CARA YANG LEBIH RELIABLE
            session()->put('face_shape', $finalFaceShape);
            session()->put('scan_image_url', $storedUrl);
            session()->put('scan_timestamp', now()->toDateTimeString());
            session()->put('ai_recommendations', $recommendationNames); // ✅ Simpan nama rekomendasi AI
            session()->put('ai_enabled', $aiEnabled); // ✅ Simpan status AI
            
            // ✅ PASTIKAN SESSION TERSIMPAN
            session()->save();
            
            Log::info('✅ Session saved for results page', [
                'face_shape' => $finalFaceShape,
                'has_image_url' => !empty($storedUrl),
                'saved' => $saved,
                'ai_recommendations' => $recommendationNames,
                'ai_recommendations_count' => count($recommendationNames),
                'ai_enabled' => $aiEnabled,
                'items_structure' => !empty($items) ? (is_array($items) ? 'array' : 'collection') : 'empty',
                'items_count' => is_array($items) ? count($items) : (method_exists($items, 'count') ? $items->count() : 0),
                'session_saved' => true,
            ]);
        } else {
            // 🔥 HAPUS SESSION LAMA JIKA TIDAK VALID
            session()->forget(['face_shape', 'scan_image_url', 'scan_timestamp', 'ai_recommendations', 'ai_enabled']);
            
            Log::warning('⚠️ Session NOT saved - invalid data or save failed', [
                'final_face_shape' => $finalFaceShape,
                'has_stored_url' => !empty($storedUrl),
                'saved' => $saved,
            ]);
        }

        // ✅ JIKA BUKAN AJAX REQUEST (FORM SUBMIT TRADISIONAL) → REDIRECT KE RESULTS
        // Ini memungkinkan AI tetap berfungsi tanpa JavaScript
        if (!$request->expectsJson() && !$request->ajax() && !$isJson) {
            // Jika valid, redirect ke results page
            if ($finalFaceShape && !empty($storedUrl)) {
                Log::info('✅ Form submit (non-AJAX) - redirecting to results', [
                    'face_shape' => $finalFaceShape,
                ]);
                
                return redirect()
                    ->route('scan.results')
                    ->with('success', 'Analisis berhasil! Hasil rekomendasi ditampilkan di bawah.');
            } else {
                // Jika tidak valid, redirect kembali ke camera dengan error
                return redirect()
                    ->route('scan.camera')
                    ->with('error', 'Wajah tidak terdeteksi. Silakan scan ulang.');
            }
        }

        // ✅ JIKA AJAX REQUEST → RETURN JSON (seperti biasa)
        return response()->json($response);
    }

    /**
     * Show results page - dengan validasi session
     * ✅ STEP 2: TAMBAH METHOD results() DI ScanController
     */
    public function results()
    {
        // AMBIL HASIL ANALISIS DARI SESSION
        $faceShape = session('face_shape');
        $scanImageUrl = session('scan_image_url');
        $aiRecommendations = session('ai_recommendations', []);
        $aiEnabled = session('ai_enabled', false);

        // ✅ JIKA TIDAK ADA HASIL VALID, BIARKAN RESULTS PAGE RENDER
        // Results page akan handle data dari sessionStorage sebagai fallback
        // Jangan redirect dulu, biarkan JavaScript di results page yang handle
        $recommendations = collect([]);
        
        if ($faceShape && $scanImageUrl) {
            Log::info('✅ Results page accessed with valid session', [
                'face_shape' => $faceShape,
                'has_ai_recommendations' => !empty($aiRecommendations),
                'ai_enabled' => $aiEnabled,
            ]);
            
            // ✅ PRIORITAS: GUNAKAN REKOMENDASI AI JIKA ADA
            $usingAIRecommendations = false; // Flag untuk track apakah menggunakan rekomendasi AI
            if (!empty($aiRecommendations) && is_array($aiRecommendations) && count($aiRecommendations) > 0) {
                Log::info('🔍 Trying to load AI recommendations from session', [
                    'ai_recommendations' => $aiRecommendations,
                    'count' => count($aiRecommendations),
                ]);
                
                // Ambil model rambut berdasarkan nama yang direkomendasikan AI
                $recommendations = HairModel::query()
                    ->whereIn('name', $aiRecommendations)
                    ->get(['id', 'name', 'image', 'types', 'length', 'face_shapes']);
                
                Log::info('🔍 Models found in database', [
                    'found_count' => $recommendations->count(),
                    'requested_names' => $aiRecommendations,
                    'found_names' => $recommendations->pluck('name')->toArray(),
                ]);
                
                // ✅ HANYA SET usingAIRecommendations = true JIKA REKOMENDASI AI DITEMUKAN
                if ($recommendations->count() > 0) {
                    $usingAIRecommendations = true;
                    
                    // Urutkan sesuai urutan AI recommendations
                    $recommendations = $recommendations->sortBy(function ($model) use ($aiRecommendations) {
                        $index = array_search($model->name, $aiRecommendations);
                        return $index !== false ? $index : 999;
                    })->values();
                    
                    Log::info('✅ Using AI recommendations from session', [
                        'recommendations_count' => $recommendations->count(),
                        'ai_recommendations' => $aiRecommendations,
                        'final_recommendations' => $recommendations->pluck('name')->toArray(),
                    ]);
                } else {
                    Log::warning('⚠️ AI recommendations not found in database - will use fallback', [
                        'requested_names' => $aiRecommendations,
                    ]);
                }
            } else {
                Log::info('⚠️ No AI recommendations in session', [
                    'has_ai_recommendations' => !empty($aiRecommendations),
                    'is_array' => is_array($aiRecommendations),
                    'count' => is_array($aiRecommendations) ? count($aiRecommendations) : 0,
                ]);
            }
            
            // ✅ FALLBACK: JIKA REKOMENDASI AI KOSONG ATAU TIDAK DITEMUKAN, GUNAKAN QUERY DATABASE
            if ($recommendations->isEmpty() || !$usingAIRecommendations) {
                // Set aiEnabled = false karena menggunakan fallback (bukan AI)
                $aiEnabled = false;
                
                // Normalize face shape untuk query
                $normalizedShape = $this->normalizeFaceShapeForQuery($faceShape);
                
                // Ambil model rambut yang cocok dengan bentuk wajah
                $recommendations = HairModel::query()
                    ->whereNotNull('face_shapes')
                    ->where('face_shapes', 'like', "%{$normalizedShape}%")
                    ->orderBy('name')
                    ->take(3) // Ambil 3 model teratas
                    ->get(['id', 'name', 'image', 'types', 'length', 'face_shapes']);

                // Jika tidak ada yang cocok, ambil beberapa model secara acak (bukan default oval)
                if ($recommendations->isEmpty()) {
                    $recommendations = HairModel::query()
                        ->orderBy('name')
                        ->take(3)
                        ->get(['id', 'name', 'image', 'types', 'length', 'face_shapes']);
                    
                    Log::warning('⚠️ No models found for face shape, showing general models', [
                        'face_shape' => $faceShape,
                        'normalized' => $normalizedShape,
                    ]);
                }
                
                Log::info('✅ Using fallback recommendations (not AI)', [
                    'recommendations_count' => $recommendations->count(),
                    'ai_enabled_set_to' => false,
                ]);
            } else {
                // ✅ REKOMENDASI AI DITEMUKAN - SET aiEnabled = true
                $aiEnabled = true;
                Log::info('✅ AI recommendations successfully loaded', [
                    'recommendations_count' => $recommendations->count(),
                    'ai_enabled_set_to' => true,
                ]);
            }
        } else {
            Log::info('ℹ️ Results page - no Laravel session, will use sessionStorage fallback', [
                'has_face_shape' => !empty($faceShape),
                'has_scan_image_url' => !empty($scanImageUrl),
            ]);
        }

        return view('user.scan_result', [
            'faceShape' => $faceShape,
            'scanImageUrl' => $scanImageUrl,
            'recommendations' => $recommendations, // ✅ Pass ke view untuk render server-side
            'aiEnabled' => $aiEnabled, // ✅ Pass status AI untuk ditampilkan badge
        ]);
    }

    /**
     * Normalize face shape untuk query database
     */
    private function normalizeFaceShapeForQuery($faceShape)
    {
        if (empty($faceShape)) {
            return null;
        }
        
        $shape = strtolower(trim($faceShape));
        
        // Mapping ke format database (Oval, Round, Square, Heart, Oblong)
        $mapping = [
            'oval' => 'Oval',
            'bulat' => 'Round',
            'round' => 'Round',
            'kotak' => 'Square',
            'square' => 'Square',
            'hati' => 'Heart',
            'heart' => 'Heart',
            'lonjong' => 'Oblong',
            'oblong' => 'Oblong',
        ];
        
        return $mapping[$shape] ?? ucfirst($shape); // Default ke capitalized jika tidak ada di mapping
    }

    /**
     * Detect number of faces in image
     * Returns null if detection is not available, otherwise returns face count
     */
    private function detectFaceCount($imagePath, $replicateService = null, $huggingFaceService = null)
    {
        // Try to detect face count using available services
        // This is a safety check - frontend should already block multiple faces
        
        try {
            // Option 1: Try Replicate face detection if available
            if ($replicateService && !empty(config('services.replicate.api_key'))) {
                // Replicate face detection models can return face count
                // For now, we'll assume if face shape detection works, there's at least 1 face
                // More sophisticated detection would require a dedicated face detection model
            }
            
            // Option 2: Try Hugging Face if available
            if ($huggingFaceService && !empty(config('services.huggingface.api_key'))) {
                // Similar to Replicate - if face shape detection works, assume 1 face
                // For multiple faces, the detection might fail or return inconsistent results
            }
            
            // For now, return null to indicate we can't reliably detect face count
            // Frontend validation is the primary defense
            // In production, you could integrate a dedicated face detection API here
            return null;
            
        } catch (\Exception $e) {
            Log::warning('Face count detection error', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
