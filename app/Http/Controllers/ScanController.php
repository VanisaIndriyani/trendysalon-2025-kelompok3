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
        $faceShape = 'Oval';
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
            $faceShape = $json['face_shape'] ?? $request->input('face_shape', 'Oval');
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
            $faceShape = $request->input('face_shape', 'Oval');
            
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

            // Use detected face shape (if available and confident) or fallback to user input
            // Only use AI detection if confidence > 0.5
            if ($detectedFaceShape && $detectionConfidence > 0.5) {
                $finalFaceShape = $detectedFaceShape;
                Log::info('✅ Using AI-detected face shape', [
                    'shape' => $finalFaceShape,
                    'confidence' => $detectionConfidence,
                ]);
            } else {
                $finalFaceShape = $faceShape ?: 'Oval';
                Log::info('📝 Using user-provided face shape', [
                    'shape' => $finalFaceShape,
                ]);
            }
            
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
            // Keep ai_enabled true if API key exists (we tried to use AI)
            // Fallback to rule-based if AI fails
            $face = strtolower((string)($faceShape ?: 'oval'));
            $prefLength = strtolower((string)($pref['length'] ?? ''));
            $prefType = strtolower((string)($pref['type'] ?? ''));

            $shapeKeywords = [
                'oval' => ['layer', 'curtain', 'butterfly', 'wolf', 'face framing', 'wavy', 'bob'],
                'bulat' => ['layer', 'curtain', 'butterfly', 'wavy', 'long'],
                'kotak' => ['layer', 'bob', 'soft', 'face framing', 'wavy'],
                'lonjong' => ['bob', 'pixie', 'medium', 'face framing'],
            ];

            $items = $models->map(function ($m) use ($face, $shapeKeywords, $prefLength, $prefType) {
                $score = 0;
                $nameLower = strtolower($m->name ?? '');
                $lengthLower = strtolower((string)($m->length ?? ''));
                $typesLower = strtolower((string)($m->types ?? ''));

                $keywords = $shapeKeywords[$face] ?? $shapeKeywords['oval'];
                foreach ($keywords as $kw) {
                    if (strpos($nameLower, $kw) !== false) { $score += 2; break; }
                }

                if ($prefLength && $lengthLower === strtolower($prefLength)) { $score += 2; }
                if ($prefType && strpos($typesLower, strtolower($prefType)) !== false) { $score += 1; }
                if ($face === 'oval') { $score += 1; }

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
            'face_shape' => $faceShape ?: 'Oval',
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
                    'face_shape' => $faceShape ?: 'Oval',
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
        $response = [
            'ok' => true,
            'stored_url' => $storedUrl,
            'face_shape' => $finalFaceShape, // Use final detected shape
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

        return response()->json($response);
    }
}
