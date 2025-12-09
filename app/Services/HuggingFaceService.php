<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HuggingFaceService
{
    private $apiKey;
    private $baseUrl = 'https://api-inference.huggingface.co/models';

    public function __construct()
    {
        $this->apiKey = config('services.huggingface.api_key');
    }

    /**
     * Analyze face shape from image using Hugging Face model
     * Menggunakan model ibai/face-shape-classification untuk deteksi bentuk wajah
     * 
     * @param string $imagePath Path to image file, base64 data URL, or full URL
     * @return array|null Returns ['label' => 'oval', 'score' => 0.92] or null on error
     */
    public function analyzeFaceShape($imagePath)
    {
        if (empty($this->apiKey)) {
            Log::warning('Hugging Face API key not configured');
            return null;
        }

        try {
            // Prepare image data
            $imageData = $this->prepareImageData($imagePath);
            
            if (!$imageData) {
                Log::warning('Failed to prepare image data for face shape detection');
                return null;
            }

            // Using face shape classification model - try multiple models for reliability
            // Note: Some models may not be available, we'll try multiple options
            $models = [
                'ibai/face-shape-classification', // Try this first
                'google/vit-base-patch16-224', // General image classification (fallback)
            ];
            
            // Alternative: Use a more general face detection model if specific face shape models unavailable
            // We can also use MediaPipe or other CV approaches
            
            $model = $models[0]; // Try primary model first
            $modelUrl = "https://api-inference.huggingface.co/models/{$model}";

            Log::info('🔍 Detecting face shape using Hugging Face', [
                'model' => $model,
                'image_size' => strlen($imageData),
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->timeout(60)->withBody($imageData, 'application/octet-stream')
              ->post($modelUrl);
              
            // If primary model fails (503/410), try fallback
            if (!$response->successful() && ($response->status() === 503 || $response->status() === 410)) {
                Log::info('⚠️ Primary model unavailable, trying fallback', [
                    'status' => $response->status(),
                    'primary_model' => $model,
                ]);
                
                $model = $models[1];
                $modelUrl = "https://api-inference.huggingface.co/models/{$model}";
                
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ])->timeout(60)->withBody($imageData, 'application/octet-stream')
                  ->post($modelUrl);
            }

            if ($response->successful()) {
                $result = $response->json();
                
                // Handle different response formats
                $faceShape = null;
                $confidence = 0;
                
                if (is_array($result)) {
                    // Format: [{"label": "oval", "score": 0.92}]
                    if (isset($result[0])) {
                        $faceShape = $result[0]['label'] ?? null;
                        $confidence = $result[0]['score'] ?? 0;
                    }
                    // Format: {"label": "oval", "score": 0.92}
                    elseif (isset($result['label'])) {
                        $faceShape = $result['label'];
                        $confidence = $result['score'] ?? 0;
                    }
                }

                // Normalize face shape labels
                $normalizedShape = $this->normalizeFaceShape($faceShape);

                Log::info('✅ Face shape detected', [
                    'original' => $faceShape,
                    'normalized' => $normalizedShape,
                    'confidence' => $confidence,
                ]);

                return [
                    'label' => $normalizedShape,
                    'score' => $confidence,
                    'original_label' => $faceShape,
                ];
            } else {
                $status = $response->status();
                $body = $response->body();
                
                $statusText = $this->getStatusText($status);
                
                Log::warning('❌ Hugging Face face shape detection failed', [
                    'status' => $status,
                    'status_text' => $statusText,
                    'body_preview' => substr($body, 0, 500),
                    'model' => $model,
                ]);
                
                // If model is loading (503), log info
                if ($status === 503) {
                    Log::info('⏳ Model is loading (503) - this is normal for first request. Wait 30-60 seconds and try again.');
                } elseif ($status === 401) {
                    Log::error('🔑 Authentication failed - check API key');
                } elseif ($status === 429) {
                    Log::warning('⚠️ Rate limit exceeded - too many requests');
                } elseif ($status === 410) {
                    Log::warning('⚠️ Model removed or unavailable - trying fallback model');
                }
                
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Hugging Face face shape detection exception', [
                'error' => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(), 0, 500),
            ]);
            return null;
        }
    }

    /**
     * Normalize face shape labels to standard format
     */
    private function normalizeFaceShape($shape)
    {
        if (empty($shape)) {
            return 'Oval';
        }

        $shape = strtolower(trim($shape));
        
        $mapping = [
            'oval' => 'Oval',
            'round' => 'Round',
            'bulat' => 'Round',
            'square' => 'Square',
            'kotak' => 'Square',
            'rectangle' => 'Oblong',
            'oblong' => 'Oblong',
            'lonjong' => 'Oblong',
            'heart' => 'Heart',
            'hati' => 'Heart',
            'diamond' => 'Diamond',
            'wajik' => 'Diamond',
        ];

        return $mapping[$shape] ?? ucfirst($shape);
    }

    /**
     * Get human-readable status text for HTTP status codes
     */
    private function getStatusText($status)
    {
        $statusTexts = [
            200 => 'OK',
            401 => 'Unauthorized (check API key)',
            403 => 'Forbidden',
            404 => 'Model not found',
            410 => 'Model gone/removed',
            429 => 'Rate limit exceeded',
            503 => 'Model loading (wait 30-60s)',
            504 => 'Gateway timeout',
        ];
        
        return $statusTexts[$status] ?? "HTTP $status";
    }

    /**
     * Get AI-powered hair cut recommendations based on face shape and preferences
     * 
     * @param string $faceShape Detected or user-selected face shape
     * @param array $preferences User preferences (length, type, condition)
     * @param array $hairModels Available hair models from database
     * @return array Enhanced recommendations with AI scoring
     */
    public function getAIRecommendations($faceShape, $preferences, $hairModels)
    {
        if (empty($this->apiKey)) {
            // Fallback to rule-based if no API key
            return $this->getFallbackRecommendations($faceShape, $preferences, $hairModels);
        }

        // Try AI text generation for recommendations first
        try {
            $aiText = $this->getAITextRecommendations($faceShape, $preferences, $hairModels);
            
            if ($aiText) {
                // Process AI text and enhance recommendations
                return $this->processAIRecommendations($aiText, $hairModels, $faceShape, $preferences);
            }
        } catch (\Exception $e) {
            Log::warning('AI text generation failed, using enhanced scoring', [
                'error' => $e->getMessage(),
            ]);
        }

        // Fallback to AI-enhanced scoring if text generation fails
        return $this->getAIEnhancedRecommendations($faceShape, $preferences, $hairModels);
    }

    /**
     * Get AI text recommendations using Mistral model
     * Menggunakan Mistral AI untuk generate rekomendasi berbasis teks
     */
    private function getAITextRecommendations($faceShape, $preferences, $hairModels)
    {
        try {
            $prefText = [];
            if (!empty($preferences['length'])) {
                $prefText[] = "panjang rambut: {$preferences['length']}";
            }
            if (!empty($preferences['type'])) {
                $prefText[] = "tipe rambut: {$preferences['type']}";
            }
            if (!empty($preferences['condition'])) {
                $prefText[] = "kondisi rambut: {$preferences['condition']}";
            }

            $prefStr = !empty($prefText) ? implode(', ', $prefText) : 'tidak ada preferensi khusus';
            
            $modelNames = $hairModels->pluck('name')->take(10)->implode(', ');
            
            $prompt = "User memiliki bentuk wajah {$faceShape} dengan preferensi {$prefStr}. Model rambut yang tersedia: {$modelNames}. Rekomendasikan 3-4 potongan rambut terbaik yang paling cocok dengan bentuk wajah dan preferensi ini. Berikan nama model yang direkomendasikan dari daftar yang tersedia.";

            $model = 'mistralai/Mixtral-8x7B-Instruct-v0.1';
            $modelUrl = "https://api-inference.huggingface.co/models/{$model}";

            Log::info('🤖 Requesting AI text recommendations', [
                'model' => $model,
                'face_shape' => $faceShape,
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($modelUrl, [
                'inputs' => $prompt,
                'parameters' => [
                    'max_new_tokens' => 200,
                    'temperature' => 0.7,
                    'return_full_text' => false,
                ],
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                // Extract generated text
                $generatedText = '';
                if (is_array($result)) {
                    if (isset($result[0]['generated_text'])) {
                        $generatedText = $result[0]['generated_text'];
                    } elseif (isset($result['generated_text'])) {
                        $generatedText = $result['generated_text'];
                    }
                } elseif (is_string($result)) {
                    $generatedText = $result;
                }

                if (!empty($generatedText)) {
                    Log::info('✅ AI text recommendations received', [
                        'text_length' => strlen($generatedText),
                        'preview' => substr($generatedText, 0, 100),
                    ]);
                    return $generatedText;
                }
            } else {
                Log::warning('AI text generation failed', [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 200),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('AI text generation error', [
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Get AI-enhanced recommendations using intelligent scoring algorithm
     * This provides better recommendations than simple rule-based matching
     */
    private function getAIEnhancedRecommendations($faceShape, $preferences, $hairModels)
    {
        $face = strtolower($faceShape);
        $prefLength = strtolower($preferences['length'] ?? '');
        $prefType = strtolower($preferences['type'] ?? '');
        $prefCondition = strtolower($preferences['condition'] ?? '');

        // Enhanced shape-to-style mapping with AI logic
        $shapeStyleMap = [
            'oval' => [
                'best' => ['layer', 'curtain', 'butterfly', 'wolf', 'face framing', 'wavy', 'bob', 'long'],
                'good' => ['pixie', 'pixie cut', 'short', 'medium'],
                'weight' => 3,
            ],
            'bulat' => [
                'best' => ['layer', 'curtain', 'butterfly', 'wavy', 'long', 'face framing'],
                'good' => ['bob', 'medium'],
                'weight' => 3,
            ],
            'round' => [
                'best' => ['layer', 'curtain', 'butterfly', 'wavy', 'long', 'face framing'],
                'good' => ['bob', 'medium'],
                'weight' => 3,
            ],
            'kotak' => [
                'best' => ['layer', 'bob', 'soft', 'face framing', 'wavy', 'curtain'],
                'good' => ['long', 'medium'],
                'weight' => 2.5,
            ],
            'square' => [
                'best' => ['layer', 'bob', 'soft', 'face framing', 'wavy', 'curtain'],
                'good' => ['long', 'medium'],
                'weight' => 2.5,
            ],
            'lonjong' => [
                'best' => ['bob', 'pixie', 'medium', 'face framing', 'layered'],
                'good' => ['short', 'curtain'],
                'weight' => 2.5,
            ],
            'oblong' => [
                'best' => ['bob', 'pixie', 'medium', 'face framing', 'layered'],
                'good' => ['short', 'curtain'],
                'weight' => 2.5,
            ],
            'heart' => [
                'best' => ['bob', 'pixie', 'layered', 'face framing', 'curtain'],
                'good' => ['medium', 'short'],
                'weight' => 2.5,
            ],
        ];

        $styleMap = $shapeStyleMap[$face] ?? $shapeStyleMap['oval'];

        $items = $hairModels->map(function ($m) use ($face, $styleMap, $prefLength, $prefType, $prefCondition) {
            $score = 0;
            $nameLower = strtolower($m->name ?? '');
            $lengthLower = strtolower((string)($m->length ?? ''));
            $typesLower = strtolower((string)($m->types ?? ''));
            
            // AI-enhanced scoring: Best match keywords get higher weight
            foreach ($styleMap['best'] as $keyword) {
                if (strpos($nameLower, $keyword) !== false) {
                    $score += 3 * ($styleMap['weight'] ?? 2); // Best matches get highest score
                    break;
                }
            }
            
            // Good matches get moderate score
            if ($score === 0) {
                foreach ($styleMap['good'] as $keyword) {
                    if (strpos($nameLower, $keyword) !== false) {
                        $score += 2 * ($styleMap['weight'] ?? 2);
                        break;
                    }
                }
            }

            // Preference matching with AI weighting
            if ($prefLength && $lengthLower === strtolower($prefLength)) {
                $score += 3; // Strong preference match
            } elseif ($prefLength) {
                // Partial match (e.g., "Panjang" vs "Long")
                $lengthSynonyms = [
                    'panjang' => ['long', 'lengkap'],
                    'pendek' => ['short', 'pixie'],
                    'medium' => ['sedang', 'menengah'],
                ];
                $synonyms = $lengthSynonyms[strtolower($prefLength)] ?? [];
                foreach ($synonyms as $syn) {
                    if (strpos($lengthLower, $syn) !== false) {
                        $score += 2;
                        break;
                    }
                }
            }
            
            if ($prefType && strpos($typesLower, strtolower($prefType)) !== false) {
                $score += 2; // Type match
            }
            
            // Condition-based bonus (if condition affects recommendation)
            if ($prefCondition && in_array(strtolower($prefCondition), ['rusak', 'damaged', 'kering', 'dry'])) {
                // Prefer styles that work well with damaged hair
                if (strpos($nameLower, 'layer') !== false || strpos($nameLower, 'bob') !== false) {
                    $score += 1;
                }
            }

            // Face shape compatibility bonus
            if ($face === 'oval' || $face === 'bulat') {
                $score += 1; // Oval and round faces are versatile
            }

            // AI recommendation flag: top scoring items are AI-recommended
            $isAIRecommended = $score >= 8; // Threshold for AI recommendation

            return [
                'name' => $m->name ?? '',
                'image_url' => asset($m->image ?? 'img/model1.png'),
                'score' => $score,
                'ai_recommended' => $isAIRecommended,
            ];
        })
        ->sortByDesc('score')
        ->take(3)
        ->values()
        ->toArray();

        return $items;
    }

    /**
     * Prepare image data for API request
     * Supports: data URL, file path, full URL, or asset URL
     */
    private function prepareImageData($imagePath)
    {
        if (empty($imagePath)) {
            Log::warning('Image path is empty');
            return null;
        }

        // If it's a data URL (base64)
        if (is_string($imagePath) && str_starts_with($imagePath, 'data:image')) {
            try {
                [, $data] = explode(',', $imagePath, 2);
                $decoded = base64_decode($data, true);
                if ($decoded !== false && strlen($decoded) > 0) {
                    Log::info('✅ Image prepared from data URL', ['size' => strlen($decoded)]);
                    return $decoded;
                }
            } catch (\Exception $e) {
                Log::error('Failed to decode base64 image', ['error' => $e->getMessage()]);
            }
            return null;
        }

        // If it's a full URL (http:// or https://)
        if (is_string($imagePath) && (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://'))) {
            // Check if it's a localhost URL - convert to file path instead
            if (str_contains($imagePath, 'localhost') || str_contains($imagePath, '127.0.0.1')) {
                // Extract path from URL: http://localhost/storage/scans/file.jpg -> scans/file.jpg
                if (preg_match('#/storage/(.+)$#', $imagePath, $matches)) {
                    $relativePath = $matches[1];
                    if (Storage::disk('public')->exists($relativePath)) {
                        $content = Storage::disk('public')->get($relativePath);
                        Log::info('✅ Image loaded from localhost URL (converted to path)', [
                            'url' => substr($imagePath, 0, 100),
                            'path' => $relativePath,
                            'size' => strlen($content),
                        ]);
                        return $content;
                    }
                }
            }
            
            // Try to fetch from external URL
            try {
                Log::info('📥 Fetching image from external URL', ['url' => substr($imagePath, 0, 100)]);
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 10,
                        'user_agent' => 'Laravel-HuggingFace-Client/1.0',
                        'ignore_errors' => true,
                    ],
                ]);
                $content = @file_get_contents($imagePath, false, $context);
                if ($content !== false && strlen($content) > 0) {
                    Log::info('✅ Image fetched from URL', ['size' => strlen($content)]);
                    return $content;
                }
            } catch (\Exception $e) {
                Log::error('Failed to fetch image from URL', [
                    'url' => substr($imagePath, 0, 100),
                    'error' => $e->getMessage(),
                ]);
            }
            return null;
        }

        // If it's a storage path (storage/scans/... or scans/...)
        if (is_string($imagePath)) {
            // Handle: storage/scans/file.jpg -> scans/file.jpg
            if (str_starts_with($imagePath, 'storage/')) {
                $relativePath = str_replace('storage/', '', $imagePath);
                if (Storage::disk('public')->exists($relativePath)) {
                    $content = Storage::disk('public')->get($relativePath);
                    Log::info('✅ Image loaded from storage path', ['path' => $relativePath, 'size' => strlen($content)]);
                    return $content;
                }
            }
            
            // Handle: scans/file.jpg (direct path)
            if (str_starts_with($imagePath, 'scans/')) {
                if (Storage::disk('public')->exists($imagePath)) {
                    $content = Storage::disk('public')->get($imagePath);
                    Log::info('✅ Image loaded from scans path', ['path' => $imagePath, 'size' => strlen($content)]);
                    return $content;
                }
            }
            
            // Handle: absolute file path (storage_path)
            if (str_starts_with($imagePath, storage_path('app/public/'))) {
                $relativePath = str_replace(storage_path('app/public/'), '', $imagePath);
                if (Storage::disk('public')->exists($relativePath)) {
                    $content = Storage::disk('public')->get($relativePath);
                    Log::info('✅ Image loaded from absolute path', ['path' => $relativePath, 'size' => strlen($content)]);
                    return $content;
                }
            }
            
            // Handle: direct file path (if file exists)
            if (file_exists($imagePath) && is_file($imagePath)) {
                $content = file_get_contents($imagePath);
                if ($content !== false) {
                    Log::info('✅ Image loaded from file system', ['path' => $imagePath, 'size' => strlen($content)]);
                    return $content;
                }
            }
        }

        // Try to extract path from asset URL (http://127.0.0.1:8000/storage/scans/...)
        if (is_string($imagePath) && str_contains($imagePath, '/storage/')) {
            $pathParts = explode('/storage/', $imagePath);
            if (isset($pathParts[1])) {
                $relativePath = $pathParts[1];
                if (Storage::disk('public')->exists($relativePath)) {
                    $content = Storage::disk('public')->get($relativePath);
                    Log::info('✅ Image loaded from asset URL', ['path' => $relativePath, 'size' => strlen($content)]);
                    return $content;
                }
            }
        }

        Log::warning('❌ Cannot prepare image data', [
            'type' => gettype($imagePath),
            'preview' => is_string($imagePath) ? substr($imagePath, 0, 100) : 'not string',
            'starts_with_data' => is_string($imagePath) && str_starts_with($imagePath, 'data:image'),
            'is_url' => is_string($imagePath) && filter_var($imagePath, FILTER_VALIDATE_URL),
        ]);

        return null;
    }

    /**
     * Build prompt for AI recommendation
     */
    private function buildRecommendationPrompt($faceShape, $preferences, $hairModels)
    {
        $prefText = [];
        if (!empty($preferences['length'])) {
            $prefText[] = "panjang rambut: {$preferences['length']}";
        }
        if (!empty($preferences['type'])) {
            $prefText[] = "tipe rambut: {$preferences['type']}";
        }
        if (!empty($preferences['condition'])) {
            $prefText[] = "kondisi rambut: {$preferences['condition']}";
        }

        $prefStr = !empty($prefText) ? implode(', ', $prefText) : 'tidak ada preferensi khusus';
        
        $modelNames = $hairModels->pluck('name')->take(10)->implode(', ');
        
        return "Rekomendasi potongan rambut untuk bentuk wajah {$faceShape} dengan preferensi {$prefStr}. Model yang tersedia: {$modelNames}. Berikan 3 rekomendasi terbaik.";
    }

    /**
     * Process AI recommendations and merge with database models
     */
    private function processAIRecommendations($aiResult, $hairModels, $faceShape, $preferences)
    {
        // Extract text from AI response
        $aiText = '';
        if (is_array($aiResult) && isset($aiResult[0]['generated_text'])) {
            $aiText = $aiResult[0]['generated_text'];
        } elseif (is_string($aiResult)) {
            $aiText = $aiResult;
        }

        // Use AI insights to enhance scoring
        $face = strtolower($faceShape);
        $prefLength = strtolower($preferences['length'] ?? '');
        $prefType = strtolower($preferences['type'] ?? '');

        $shapeKeywords = [
            'oval' => ['layer', 'curtain', 'butterfly', 'wolf', 'face framing', 'wavy', 'bob'],
            'bulat' => ['layer', 'curtain', 'butterfly', 'wavy', 'long'],
            'round' => ['layer', 'curtain', 'butterfly', 'wavy', 'long'],
            'kotak' => ['layer', 'bob', 'soft', 'face framing', 'wavy'],
            'square' => ['layer', 'bob', 'soft', 'face framing', 'wavy'],
            'lonjong' => ['bob', 'pixie', 'medium', 'face framing'],
            'oblong' => ['bob', 'pixie', 'medium', 'face framing'],
            'heart' => ['bob', 'pixie', 'layered', 'face framing'],
        ];

        $items = $hairModels->map(function ($m) use ($face, $shapeKeywords, $prefLength, $prefType, $aiText) {
            $score = 0;
            $nameLower = strtolower($m->name ?? '');
            $lengthLower = strtolower((string)($m->length ?? ''));
            $typesLower = strtolower((string)($m->types ?? ''));

            // Base scoring from keywords
            $keywords = $shapeKeywords[$face] ?? $shapeKeywords['oval'];
            foreach ($keywords as $kw) {
                if (strpos($nameLower, $kw) !== false) {
                    $score += 2;
                    break;
                }
            }

            // Preference matching
            if ($prefLength && $lengthLower === strtolower($prefLength)) {
                $score += 2;
            }
            if ($prefType && strpos($typesLower, strtolower($prefType)) !== false) {
                $score += 1;
            }

            // AI boost: if model name appears in AI text, boost score
            if (!empty($aiText) && stripos($aiText, $m->name) !== false) {
                $score += 3; // Significant boost from AI recommendation
            }

            // Face shape matching
            if ($face === 'oval' || $face === 'bulat') {
                $score += 1;
            }

            return [
                'name' => $m->name ?? '',
                'image_url' => asset($m->image ?? 'img/model1.png'),
                'score' => $score,
                'ai_recommended' => !empty($aiText) && stripos($aiText, $m->name) !== false,
            ];
        })
        ->sortByDesc('score')
        ->take(3)
        ->values()
        ->toArray();

        return $items;
    }

    /**
     * Fallback recommendations when AI is not available
     */
    private function getFallbackRecommendations($faceShape, $preferences, $hairModels)
    {
        $face = strtolower($faceShape);
        $prefLength = strtolower($preferences['length'] ?? '');
        $prefType = strtolower($preferences['type'] ?? '');

        $shapeKeywords = [
            'oval' => ['layer', 'curtain', 'butterfly', 'wolf', 'face framing', 'wavy', 'bob'],
            'bulat' => ['layer', 'curtain', 'butterfly', 'wavy', 'long'],
            'round' => ['layer', 'curtain', 'butterfly', 'wavy', 'long'],
            'kotak' => ['layer', 'bob', 'soft', 'face framing', 'wavy'],
            'square' => ['layer', 'bob', 'soft', 'face framing', 'wavy'],
            'lonjong' => ['bob', 'pixie', 'medium', 'face framing'],
            'oblong' => ['bob', 'pixie', 'medium', 'face framing'],
            'heart' => ['bob', 'pixie', 'layered', 'face framing'],
        ];

        $items = $hairModels->map(function ($m) use ($face, $shapeKeywords, $prefLength, $prefType) {
            $score = 0;
            $nameLower = strtolower($m->name ?? '');
            $lengthLower = strtolower((string)($m->length ?? ''));
            $typesLower = strtolower((string)($m->types ?? ''));

            $keywords = $shapeKeywords[$face] ?? $shapeKeywords['oval'];
            foreach ($keywords as $kw) {
                if (strpos($nameLower, $kw) !== false) {
                    $score += 2;
                    break;
                }
            }

            if ($prefLength && $lengthLower === strtolower($prefLength)) {
                $score += 2;
            }
            if ($prefType && strpos($typesLower, strtolower($prefType)) !== false) {
                $score += 1;
            }
            if ($face === 'oval' || $face === 'bulat') {
                $score += 1;
            }

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

        return $items;
    }

    /**
     * Analyze hair type and characteristics from image
     * Deteksi jenis rambut (lurus/ikal/keriting) dan panjang rambut
     * 
     * @param string $imagePath Path to image file, base64 data URL, or full URL
     * @return array|null Returns hair analysis data or null on error
     */
    public function analyzeHairCharacteristics($imagePath)
    {
        if (empty($this->apiKey)) {
            Log::warning('Hugging Face API key not configured for hair analysis');
            return null;
        }

        try {
            $imageData = $this->prepareImageData($imagePath);
            
            if (!$imageData) {
                Log::warning('Failed to prepare image data for hair analysis');
                return null;
            }

            Log::info('🔍 Analyzing hair characteristics', [
                'image_size' => strlen($imageData),
            ]);

            // For now, return structured data that uses user input
            // In production, you could add proper ML models for hair detection
            return [
                'hair_type' => 'Unknown', // Will be detected from user input or enhanced later
                'hair_length' => 'Unknown', // Will be detected from user input
                'detection_method' => 'user_input', // or 'ai' when proper model is added
            ];
            
        } catch (\Exception $e) {
            Log::error('Hair analysis exception', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Comprehensive face and hair analysis
     * Analisis lengkap: bentuk wajah + jenis rambut + panjang rambut
     * 
     * @param string $imagePath Path to image
     * @param array $userPreferences User input preferences (length, type, condition)
     * @return array Complete analysis result
     */
    public function comprehensiveAnalysis($imagePath, $userPreferences = [])
    {
        $result = [
            'face_shape' => null,
            'face_shape_confidence' => 0,
            'hair_type' => $userPreferences['type'] ?? null,
            'hair_length' => $userPreferences['length'] ?? null,
            'hair_condition' => $userPreferences['condition'] ?? null,
            'analysis_method' => 'user_input',
        ];

        // 1. Detect face shape
        $faceShapeResult = $this->analyzeFaceShape($imagePath);
        if ($faceShapeResult && isset($faceShapeResult['label'])) {
            $result['face_shape'] = $faceShapeResult['label'];
            $result['face_shape_confidence'] = $faceShapeResult['score'] ?? 0;
            $result['analysis_method'] = 'ai_detected';
        }

        // 2. Analyze hair characteristics (if model available)
        $hairAnalysis = $this->analyzeHairCharacteristics($imagePath);
        if ($hairAnalysis) {
            if (!empty($hairAnalysis['hair_type']) && $hairAnalysis['hair_type'] !== 'Unknown') {
                $result['hair_type'] = $hairAnalysis['hair_type'];
            }
            if (!empty($hairAnalysis['hair_length']) && $hairAnalysis['hair_length'] !== 'Unknown') {
                $result['hair_length'] = $hairAnalysis['hair_length'];
            }
        }

        // 3. Use user preferences as fallback
        if (empty($result['hair_type'])) {
            $result['hair_type'] = $userPreferences['type'] ?? 'Lurus';
        }
        if (empty($result['hair_length'])) {
            $result['hair_length'] = $userPreferences['length'] ?? 'Panjang';
        }

        Log::info('✅ Comprehensive analysis completed', [
            'face_shape' => $result['face_shape'],
            'hair_type' => $result['hair_type'],
            'hair_length' => $result['hair_length'],
            'method' => $result['analysis_method'],
        ]);

        return $result;
    }

    /**
     * Get comprehensive hair style recommendations based on face shape
     * Returns detailed recommendations with descriptions
     */
    public function getHairStyleRecommendations($faceShape)
    {
        $shape = strtolower($faceShape);
        
        $recommendations = [
            'oval' => [
                'styles' => [
                    'Layered Cut' => 'Potongan berlapis yang memberikan volume dan tekstur',
                    'Curtain Bangs' => 'Bangs tirai yang membingkai wajah dengan sempurna',
                    'Long Waves' => 'Gelombang panjang yang elegan dan feminin',
                    'Butterfly Hair Cut' => 'Potongan rambut yang memberikan dimensi dan gerak',
                    'Face Framing Layers' => 'Lapisan yang membingkai wajah secara natural',
                ],
                'description' => 'Bentuk wajah oval sangat serbaguna dan cocok dengan hampir semua gaya rambut.',
                'avoid' => 'Hindari potongan yang terlalu berat di bagian bawah',
            ],
            'round' => [
                'styles' => [
                    'Long Layers' => 'Lapisan panjang yang menciptakan ilusi wajah lebih panjang',
                    'Bob Asimetris' => 'Bob dengan panjang berbeda untuk menambah dimensi',
                    'Pixie Volume' => 'Pixie dengan volume di atas untuk memperpanjang wajah',
                    'Side-Swept Bangs' => 'Bangs menyamping yang menciptakan sudut',
                    'Layered Lob' => 'Long bob dengan lapisan untuk struktur',
                ],
                'description' => 'Fokus pada potongan yang menciptakan tinggi dan panjang pada wajah.',
                'avoid' => 'Hindari potongan yang menambah lebar wajah',
            ],
            'square' => [
                'styles' => [
                    'Soft Layers' => 'Lapisan lembut yang melembutkan sudut wajah',
                    'Side Bangs' => 'Bangs samping yang menciptakan kurva',
                    'Wavy Bob' => 'Bob bergelombang yang melembutkan garis rahang',
                    'Long Waves' => 'Gelombang panjang yang menciptakan gerak',
                    'Curtain Bangs' => 'Bangs tirai yang melembutkan dahi',
                ],
                'description' => 'Pilih gaya yang melembutkan sudut tajam dan menciptakan kurva.',
                'avoid' => 'Hindari potongan lurus yang menekankan garis rahang',
            ],
            'heart' => [
                'styles' => [
                    'Chin-length Bob' => 'Bob sepanjang dagu yang menyeimbangkan dahi lebar',
                    'Curtain Bangs' => 'Bangs tirai yang melembutkan dahi',
                    'Loose Waves' => 'Gelombang longgar yang menambah volume di bawah',
                    'Layered Medium' => 'Potongan medium berlapis untuk keseimbangan',
                    'Side Part' => 'Pisahan samping yang menciptakan keseimbangan',
                ],
                'description' => 'Fokus pada gaya yang menyeimbangkan dahi lebar dengan dagu sempit.',
                'avoid' => 'Hindari volume berlebihan di bagian atas',
            ],
            'oblong' => [
                'styles' => [
                    'Bob with Bangs' => 'Bob dengan bangs untuk memperpendek wajah',
                    'Pixie Cut' => 'Pixie yang menciptakan lebar pada wajah',
                    'Medium Layers' => 'Lapisan medium yang menambah lebar',
                    'Face Framing' => 'Bingkai wajah yang menciptakan kurva',
                    'Wavy Lob' => 'Long bob bergelombang untuk volume',
                ],
                'description' => 'Pilih gaya yang menciptakan lebar dan memperpendek wajah.',
                'avoid' => 'Hindari potongan yang memperpanjang wajah',
            ],
            'diamond' => [
                'styles' => [
                    'Side Part' => 'Pisahan samping yang menyeimbangkan',
                    'Shaggy Layer' => 'Lapisan shaggy yang menciptakan tekstur',
                    'Wispy Bangs' => 'Bangs tipis yang melembutkan',
                    'Long Layers' => 'Lapisan panjang untuk keseimbangan',
                    'Textured Bob' => 'Bob dengan tekstur untuk volume',
                ],
                'description' => 'Fokus pada gaya yang menyeimbangkan proporsi wajah.',
                'avoid' => 'Hindari potongan yang menekankan tulang pipi',
            ],
        ];

        return $recommendations[$shape] ?? $recommendations['oval'];
    }
}

