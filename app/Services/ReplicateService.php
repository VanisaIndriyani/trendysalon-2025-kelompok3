<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReplicateService
{
    private $apiKey;
    private $baseUrl = 'https://api.replicate.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.replicate.api_key');
    }

    /**
     * Analyze face shape from image using Replicate
     * Replicate memiliki banyak model untuk face detection dan classification
     * 
     * @param string $imagePath Path to image file, base64 data URL, or full URL
     * @return array|null Returns ['label' => 'oval', 'score' => 0.92] or null on error
     */
    public function analyzeFaceShape($imagePath)
    {
        if (empty($this->apiKey)) {
            Log::warning('Replicate API key not configured');
            return null;
        }

        try {
            // Prepare image data - Replicate accepts URLs or base64
            $imageUrl = $this->prepareImageForReplicate($imagePath);
            
            if (!$imageUrl) {
                Log::warning('Failed to prepare image for Replicate');
                return null;
            }

            // Replicate models untuk face detection/shape classification
            // Beberapa model yang bisa digunakan:
            // - face-detection, face-landmarks, face-shape-classification
            // Kita akan gunakan model yang tersedia dan bekerja
            
            Log::info('🔍 Detecting face shape using Replicate', [
                'image_url' => is_string($imageUrl) ? substr($imageUrl, 0, 100) : 'base64',
            ]);

            // Option 1: Gunakan model face detection + custom logic
            // Option 2: Gunakan model yang sudah ada untuk face shape
            // Kita akan coba beberapa approach

            // Approach 1: Gunakan face detection model untuk detect landmarks
            // Kemudian analisis landmarks untuk determine face shape
            $result = $this->detectFaceWithReplicate($imageUrl);
            
            if ($result) {
                // Extract face shape dari hasil detection
                $faceShape = $this->determineFaceShapeFromDetection($result);
                
                if ($faceShape) {
                    Log::info('✅ Face shape detected via Replicate', [
                        'face_shape' => $faceShape,
                    ]);
                    
                    return [
                        'label' => $faceShape,
                        'score' => 0.85, // Default confidence
                        'source' => 'replicate',
                    ];
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Replicate face shape detection exception', [
                'error' => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(), 0, 500),
            ]);
            return null;
        }
    }

    /**
     * Detect face using Replicate face detection model
     */
    private function detectFaceWithReplicate($imageUrl)
    {
        try {
            // Model untuk face detection: banyak pilihan di Replicate
            // Contoh: lucataco/face-detection, atau model lain
            // Kita akan gunakan model yang umum dan tersedia
            
            // Replicate API format:
            // POST https://api.replicate.com/v1/predictions
            // {
            //   "version": "model_version_id",
            //   "input": { "image": "url_or_base64" }
            // }

            // Untuk sekarang, kita akan gunakan model yang tersedia
            // User bisa update model version sesuai yang tersedia di Replicate
            
            $modelVersion = config('services.replicate.face_detection_model', 'lucataco/face-detection');
            
            Log::info('📡 Calling Replicate API', [
                'model' => $modelVersion,
            ]);

            // Create prediction
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(90)->post("{$this->baseUrl}/predictions", [
                'version' => $modelVersion,
                'input' => [
                    'image' => $imageUrl,
                ],
            ]);

            if ($response->successful()) {
                $prediction = $response->json();
                $predictionId = $prediction['id'] ?? null;
                
                if ($predictionId) {
                    // Poll for result (Replicate is async)
                    return $this->pollPredictionResult($predictionId);
                }
            } else {
                Log::warning('❌ Replicate API call failed', [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500),
                ]);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Replicate face detection failed', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Poll for prediction result (Replicate is async)
     */
    private function pollPredictionResult($predictionId, $maxAttempts = 10)
    {
        $attempt = 0;
        
        while ($attempt < $maxAttempts) {
            sleep(2); // Wait 2 seconds between polls
            
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
            ])->timeout(30)->get("{$this->baseUrl}/predictions/{$predictionId}");

            if ($response->successful()) {
                $result = $response->json();
                $status = $result['status'] ?? null;
                
                if ($status === 'succeeded') {
                    return $result['output'] ?? null;
                } elseif ($status === 'failed') {
                    Log::error('Replicate prediction failed', [
                        'error' => $result['error'] ?? 'Unknown error',
                    ]);
                    return null;
                }
                // If still processing, continue polling
            }
            
            $attempt++;
        }
        
        Log::warning('Replicate prediction timeout', [
            'prediction_id' => $predictionId,
            'attempts' => $attempt,
        ]);
        
        return null;
    }

    /**
     * Determine face shape from face detection result
     * Analyze face landmarks/features to determine shape
     */
    private function determineFaceShapeFromDetection($detectionResult)
    {
        // Jika detection result berisi landmarks atau measurements
        // Kita bisa analisis untuk determine face shape
        
        // Untuk sekarang, kita akan return null dan gunakan fallback
        // User bisa enhance ini dengan logic yang lebih detail
        
        // Contoh logic (simplified):
        // - Jika ada landmarks, analisis rasio width/height
        // - Bandingkan dengan karakteristik setiap face shape
        // - Return shape yang paling cocok
        
        return null; // Will use fallback
    }

    /**
     * Prepare image for Replicate API
     * Replicate accepts: URL, base64, or file upload
     */
    private function prepareImageForReplicate($imagePath)
    {
        // If it's already a URL (external)
        if (is_string($imagePath) && (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://'))) {
            // Check if it's localhost - Replicate can't access localhost
            if (str_contains($imagePath, 'localhost') || str_contains($imagePath, '127.0.0.1')) {
                // Convert to base64 instead
                return $this->convertLocalImageToBase64($imagePath);
            }
            return $imagePath; // External URL is fine
        }

        // If it's a data URL (base64)
        if (is_string($imagePath) && str_starts_with($imagePath, 'data:image')) {
            return $imagePath; // Replicate accepts data URLs
        }

        // If it's a file path, convert to base64
        if (is_string($imagePath)) {
            return $this->convertLocalImageToBase64($imagePath);
        }

        return null;
    }

    /**
     * Convert local image to base64 data URL
     */
    private function convertLocalImageToBase64($imagePath)
    {
        try {
            $imageData = null;
            
            // If it's a data URL (base64)
            if (is_string($imagePath) && str_starts_with($imagePath, 'data:image')) {
                return $imagePath; // Already base64
            }

            // If it's a full URL (http:// or https://)
            if (is_string($imagePath) && (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://'))) {
                // Check if it's localhost - convert to base64
                if (str_contains($imagePath, 'localhost') || str_contains($imagePath, '127.0.0.1')) {
                    // Extract path from URL
                    if (preg_match('#/storage/(.+)$#', $imagePath, $matches)) {
                        $relativePath = $matches[1];
                        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($relativePath)) {
                            $imageData = \Illuminate\Support\Facades\Storage::disk('public')->get($relativePath);
                        }
                    }
                } else {
                    // External URL - try to fetch
                    try {
                        $context = stream_context_create([
                            'http' => [
                                'timeout' => 10,
                                'user_agent' => 'Laravel-Replicate-Client/1.0',
                            ],
                        ]);
                        $imageData = @file_get_contents($imagePath, false, $context);
                    } catch (\Exception $e) {
                        Log::error('Failed to fetch image from URL', ['error' => $e->getMessage()]);
                    }
                }
            }
            
            // If it's a storage path
            if (!$imageData && is_string($imagePath)) {
                if (str_starts_with($imagePath, 'storage/')) {
                    $relativePath = str_replace('storage/', '', $imagePath);
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($relativePath)) {
                        $imageData = \Illuminate\Support\Facades\Storage::disk('public')->get($relativePath);
                    }
                } elseif (str_starts_with($imagePath, 'scans/')) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($imagePath)) {
                        $imageData = \Illuminate\Support\Facades\Storage::disk('public')->get($imagePath);
                    }
                } elseif (file_exists($imagePath) && is_file($imagePath)) {
                    $imageData = file_get_contents($imagePath);
                }
            }
            
            if ($imageData && strlen($imageData) > 0) {
                // Determine MIME type
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_buffer($finfo, $imageData);
                finfo_close($finfo);
                
                if (!$mimeType) {
                    $mimeType = 'image/jpeg'; // Default
                }
                
                $base64 = base64_encode($imageData);
                return "data:{$mimeType};base64,{$base64}";
            }
        } catch (\Exception $e) {
            Log::error('Failed to convert image to base64', [
                'error' => $e->getMessage(),
            ]);
        }
        
        return null;
    }

    /**
     * Comprehensive analysis using Replicate
     * Similar to HuggingFaceService comprehensiveAnalysis
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

        // Try to detect face shape
        $faceShapeResult = $this->analyzeFaceShape($imagePath);
        if ($faceShapeResult && isset($faceShapeResult['label'])) {
            $result['face_shape'] = $faceShapeResult['label'];
            $result['face_shape_confidence'] = $faceShapeResult['score'] ?? 0;
            $result['analysis_method'] = 'replicate_ai';
        }

        // Use user preferences as fallback
        if (empty($result['hair_type'])) {
            $result['hair_type'] = $userPreferences['type'] ?? 'Lurus';
        }
        if (empty($result['hair_length'])) {
            $result['hair_length'] = $userPreferences['length'] ?? 'Panjang';
        }

        Log::info('✅ Replicate comprehensive analysis completed', [
            'face_shape' => $result['face_shape'],
            'hair_type' => $result['hair_type'],
            'hair_length' => $result['hair_length'],
            'method' => $result['analysis_method'],
        ]);

        return $result;
    }

    /**
     * Normalize face shape labels
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
}

