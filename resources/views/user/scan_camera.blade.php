<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
        <title>Scan Wajah & Rambut</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;600;700&display=swap" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    fontFamily: {
                        sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji']
                    }
                }
            };
        </script>
        <script>
            // Expose absolute URLs for scan routes so JS can navigate correctly under subdirectories
            window.__SCAN_ROUTES__ = {
                camera: "{{ url('/scan/camera') }}",
                results: "{{ url('/scan/results') }}",
                analyze: "{{ url('/scan/analyze') }}", // ✅ Route untuk kirim foto ke backend
            };
        </script>
        <script src="{{ asset('build/assets/app-CWlvN2px.js') }}" defer></script>
    </head>
    <body class="bg-stone-200 font-sans text-stone-800" id="scanCameraPage">
        <!-- Header -->
        <header class="sticky top-0 z-30 bg-pink-200/90 backdrop-blur border-b border-pink-300/40 transition-all duration-300 shadow-sm">
            <div class="mx-auto max-w-screen-md px-3 sm:px-4 py-2.5 sm:py-3 flex items-center gap-2 sm:gap-3">
                <a href="{{ route('scan') }}" class="grid h-8 w-8 sm:h-9 sm:w-9 place-items-center rounded-lg border border-stone-300 bg-pink-100/60 text-stone-700 touch-manipulation transition-all duration-300 hover:bg-pink-200 hover:scale-110 hover:shadow-md group" aria-label="Kembali">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 sm:h-5 sm:w-5 transition-transform duration-300 group-hover:-translate-x-1"><path d="M15 6l-6 6 6 6" stroke-width="1.5"/></svg>
                </a>
                <h1 class="text-base sm:text-lg font-semibold bg-gradient-to-r from-pink-600 to-amber-600 bg-clip-text text-transparent animate-gradient">Scan Wajah & Rambut</h1>
            </div>
        </header>

        <main class="mx-auto max-w-screen-md px-3 sm:px-4">
            <section class="mt-3 sm:mt-4 animate-fade-in-up">
                <div id="faceInstruction" class="rounded-2xl bg-gradient-to-br from-green-100 via-emerald-50 to-green-100 px-4 sm:px-5 py-4 sm:py-5 text-center shadow-lg border border-green-200/50 transition-all duration-300">
                    <div class="flex items-center justify-center gap-2 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 text-green-600">
                            <path d="M12 2v20M2 12h20" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <p class="font-bold text-sm sm:text-base text-green-800">Posisikan Wajah di Dalam Oval Hijau</p>
                    </div>
                    <p class="text-xs sm:text-sm text-green-700 leading-relaxed">Tetap diam dan pastikan wajah terlihat jelas untuk hasil terbaik</p>
                </div>

                <!-- ✅ KAMERA LIVE (DENGAN JS) -->
                <div class="mt-4 sm:mt-5 rounded-2xl bg-gradient-to-br from-stone-100 to-stone-200 p-3 sm:p-5 shadow-xl border border-stone-300/50 hover:shadow-2xl transition-all duration-300">
                    <div class="rounded-2xl border-3 border-sky-400 bg-gradient-to-br from-stone-50 to-stone-100 grid place-items-center h-[300px] sm:h-[380px] md:h-[440px] relative overflow-hidden animate-border-pulse shadow-inner">
                        <!-- Animated border effect -->
                        <div class="absolute inset-0 rounded-xl border-2 border-pink-400 opacity-0 animate-border-glow"></div>
                        
                        <video id="cameraVideo" autoplay playsinline class="h-full w-full object-cover rounded-lg hidden transition-opacity duration-500" style="transform: scaleX(1) !important;"></video>
                        
                        <!-- Face Alignment Guide Overlay - OVAL HIJAU BESAR -->
                        <div id="faceGuideOverlay" class="absolute inset-0 pointer-events-none z-10 hidden">
                            <!-- Container untuk oval guide - AKAN DIROTASI OLEH JS -->
                            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 transition-transform duration-300" style="transform-origin: center center;">
                                <!-- ✅ OVAL HIJAU BESAR SEBAGAI FRAME WAJAH -->
                                <div class="w-[280px] h-[360px] sm:w-[320px] sm:h-[420px] md:w-[360px] md:h-[480px] border-4 border-green-500 rounded-[50%] shadow-2xl shadow-green-500/50 bg-transparent"></div>
                                
                                <!-- ✅ GARIS TENGAH HORIZONTAL (UNTUK MATA) -->
                                <div class="absolute top-[38%] left-1/2 transform -translate-x-1/2 w-[85%] h-0.5 bg-green-400/60"></div>
                                
                                <!-- ✅ GARIS TENGAH VERTIKAL (UNTUK HIDUNG) -->
                                <div class="absolute left-1/2 top-[10%] bottom-[10%] transform -translate-x-1/2 w-0.5 bg-green-400/60"></div>
                                
                                <!-- ✅ TITIK TENGAH (UNTUK HIDUNG) -->
                                <div class="absolute top-[45%] left-1/2 w-3 h-3 bg-green-500 rounded-full transform -translate-x-1/2 -translate-y-1/2 shadow-lg shadow-green-500/70"></div>
                            </div>
                        </div>
                        
                        <!-- Warning Multiple Faces -->
                        <div id="multipleFacesWarning" class="absolute top-2 left-2 right-2 bg-red-600 text-white px-4 py-3 rounded-lg shadow-2xl z-30 border-2 border-red-700 animate-pulse-slow hidden">
                            <div class="flex items-start gap-3">
                                <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm sm:text-base font-bold mb-1">❌ GAGAL: Terdeteksi Lebih dari 1 Wajah</p>
                                    <p class="text-xs sm:text-sm font-medium opacity-95">AI tidak dapat menganalisis jika ada 2 orang atau lebih dalam foto. Pastikan hanya 1 wajah yang terlihat di kamera.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div id="cameraPlaceholder" class="grid place-items-center animate-fade-in">
                            <div class="relative">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="h-20 w-20 sm:h-24 sm:w-24 border-4 border-pink-400 rounded-full animate-spin-slow"></div>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-12 w-12 sm:h-16 sm:w-16 text-pink-500 animate-pulse-slow relative z-10"><path d="M4 7h3l2-2h6l2 2h3a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2Z" stroke-width="1.5"/><circle cx="12" cy="13" r="3.5" stroke-width="1.5"/></svg>
                            </div>
                            <p class="mt-6 text-sm sm:text-base font-medium text-stone-700 animate-fade-in">Memuat kamera...</p>
                        </div>
                        <canvas id="cameraCanvas" class="hidden"></canvas>
                    </div>
                </div>

                <div class="mt-6 sm:mt-8 flex justify-center relative" style="z-index: 999;">
                    <button id="captureBtn" type="button" aria-label="Ambil foto" class="group relative grid h-24 w-24 sm:h-28 sm:w-28 place-items-center rounded-full bg-gradient-to-br from-pink-500 via-pink-500 to-rose-500 ring-4 ring-pink-200 shadow-2xl transition-all duration-300 active:scale-95 touch-manipulation hover:scale-110 hover:shadow-2xl hover:ring-pink-300 animate-pulse-slow" style="pointer-events: auto !important; z-index: 999 !important; position: relative !important; cursor: pointer !important; -webkit-tap-highlight-color: transparent; opacity: 1 !important; disabled: false !important;">
                        <!-- Outer ring animation -->
                        <div class="absolute inset-0 rounded-full border-4 border-pink-300 opacity-0 group-hover:opacity-100 group-hover:scale-125 transition-all duration-500"></div>
                        
                        <!-- Inner glow -->
                        <div class="absolute inset-2 rounded-full bg-white/30 blur-sm group-hover:bg-white/50 transition-all duration-300"></div>
                        
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-12 w-12 sm:h-14 sm:w-14 text-white relative z-10 transition-transform duration-300 group-hover:scale-110 pointer-events-none" stroke-width="2">
                            <path d="M4 7h3l2-2h6l2 2h3a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2Z"/>
                            <circle cx="12" cy="13" r="3.5"/>
                        </svg>
                        
                        <!-- Ripple effect on click -->
                        <div class="absolute inset-0 rounded-full bg-white/50 scale-0 group-active:scale-150 group-active:opacity-0 transition-all duration-500 pointer-events-none"></div>
                    </button>
                </div>
                
                <!-- Helper text -->
                <p class="mt-5 sm:mt-6 text-center text-xs sm:text-sm text-stone-700 font-medium animate-fade-in">
                    Pastikan wajah berada di dalam oval hijau, lalu tekan tombol untuk mengambil foto
                </p>
            </section>
        </main>

        <!-- Notification Toast Container -->
        <div id="notificationToast" class="fixed top-20 left-1/2 transform -translate-x-1/2 z-[9999] hidden transition-all duration-300 pointer-events-auto" style="touch-action: manipulation;">
            <div id="toastContainer" class="bg-white rounded-xl shadow-2xl border-2 border-stone-200 min-w-[300px] max-w-[90vw] px-4 py-3 animate-slide-in cursor-pointer" style="pointer-events: auto;">
                <div class="flex items-start gap-3">
                    <div id="toastIcon" class="flex-shrink-0 mt-0.5"></div>
                    <div class="flex-1 min-w-0">
                        <p id="toastTitle" class="font-semibold text-sm text-stone-900"></p>
                        <p id="toastMessage" class="text-xs text-stone-600 mt-1 break-words"></p>
                    </div>
                    <button id="toastClose" class="flex-shrink-0 text-stone-400 hover:text-stone-600 active:text-stone-800 transition-colors touch-manipulation p-2 -m-2" aria-label="Tutup" style="min-width: 44px; min-height: 44px; pointer-events: auto; z-index: 10000;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- ✅ MODAL PINK UNTUK ERROR FACE DETECTION -->
        <div id="faceDetectionModal" class="fixed inset-0 z-[10000] hidden items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity duration-300" style="pointer-events: auto;">
            <div class="relative mx-4 w-full max-w-md rounded-2xl bg-gradient-to-br from-pink-50 via-pink-100 to-rose-50 shadow-2xl border-2 border-pink-300 animate-fade-in-up" style="pointer-events: auto;">
                <!-- Close button -->
                <button id="faceDetectionModalClose" class="absolute top-3 right-3 z-10 grid h-8 w-8 place-items-center rounded-full bg-pink-200/80 text-pink-700 transition-all duration-300 hover:bg-pink-300 hover:scale-110 active:scale-95 touch-manipulation" aria-label="Tutup" style="pointer-events: auto;">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                
                <!-- Modal content -->
                <div class="px-6 py-6 sm:px-8 sm:py-8">
                    <!-- Icon -->
                    <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-full bg-gradient-to-br from-pink-400 to-pink-500 shadow-lg">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    
                    <!-- Title -->
                    <h3 class="mb-3 text-center text-lg font-bold text-pink-800 sm:text-xl">
                        ⚠️ Wajah Tidak Terdeteksi
                    </h3>
                    
                    <!-- Message -->
                    <p class="mb-6 text-center text-sm text-pink-700 sm:text-base leading-relaxed">
                        Hadap kamera dan pastikan wajah terlihat jelas di dalam oval hijau.
                    </p>
                    
                    <!-- Action buttons -->
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button id="faceDetectionModalOk" class="flex-1 rounded-xl bg-gradient-to-r from-pink-500 to-pink-600 px-4 py-3 text-sm font-semibold text-white shadow-md transition-all duration-300 hover:from-pink-600 hover:to-pink-700 hover:shadow-lg active:scale-95 touch-manipulation" style="pointer-events: auto;">
                            Mengerti
                        </button>
                        <a href="{{ route('scan') }}" class="flex-1 rounded-xl border-2 border-pink-300 bg-white px-4 py-3 text-center text-sm font-semibold text-pink-700 transition-all duration-300 hover:bg-pink-50 hover:border-pink-400 active:scale-95 touch-manipulation" style="pointer-events: auto;">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ✅ MODAL PINK UNTUK MULTIPLE FACES WARNING -->
        <div id="multipleFacesModal" class="fixed inset-0 z-[10000] hidden items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity duration-300" style="pointer-events: auto;">
            <div class="relative mx-4 w-full max-w-md rounded-2xl bg-gradient-to-br from-pink-50 via-pink-100 to-rose-50 shadow-2xl border-2 border-pink-300 animate-fade-in-up" style="pointer-events: auto;">
                <!-- Close button -->
                <button id="multipleFacesModalClose" class="absolute top-3 right-3 z-10 grid h-8 w-8 place-items-center rounded-full bg-pink-200/80 text-pink-700 transition-all duration-300 hover:bg-pink-300 hover:scale-110 active:scale-95 touch-manipulation" aria-label="Tutup" style="pointer-events: auto;">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                
                <!-- Modal content -->
                <div class="px-6 py-6 sm:px-8 sm:py-8">
                    <!-- Icon -->
                    <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-full bg-gradient-to-br from-pink-400 to-pink-500 shadow-lg">
                        <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    
                    <!-- Title -->
                    <h3 id="multipleFacesModalTitle" class="mb-3 text-center text-lg font-bold text-pink-800 sm:text-xl">
                        ❌ GAGAL: Terdeteksi Lebih dari 1 Wajah
                    </h3>
                    
                    <!-- Message -->
                    <p id="multipleFacesModalMessage" class="mb-6 text-center text-sm text-pink-700 sm:text-base leading-relaxed">
                        AI tidak dapat menganalisis jika ada 2 orang atau lebih dalam foto. Pastikan hanya 1 wajah yang terlihat di kamera.
                    </p>
                    
                    <!-- Action buttons -->
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button id="multipleFacesModalOk" class="flex-1 rounded-xl bg-gradient-to-r from-pink-500 to-pink-600 px-4 py-3 text-sm font-semibold text-white shadow-md transition-all duration-300 hover:from-pink-600 hover:to-pink-700 hover:shadow-lg active:scale-95 touch-manipulation" style="pointer-events: auto;">
                            Mengerti
                        </button>
                        <button id="multipleFacesModalRetry" class="flex-1 rounded-xl border-2 border-pink-300 bg-white px-4 py-3 text-sm font-semibold text-pink-700 transition-all duration-300 hover:bg-pink-50 hover:border-pink-400 active:scale-95 touch-manipulation" style="pointer-events: auto;">
                            Coba Lagi
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced styles with animations -->
        <style>
            /* Fade in animations */
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            @keyframes pulseSlow {
                0%, 100% { opacity: 1; transform: scale(1); }
                50% { opacity: 0.9; transform: scale(1.02); }
            }
            
            @keyframes borderPulse {
                0%, 100% { border-color: rgb(14 165 233); }
                50% { border-color: rgb(236 72 153); }
            }
            
            @keyframes borderGlow {
                0%, 100% { opacity: 0; }
                50% { opacity: 1; }
            }
            
            @keyframes spinSlow {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            
            @keyframes gradient {
                0%, 100% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
            }
            
            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateX(-50%) translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateX(-50%) translateY(0);
                }
            }
            
            .animate-fade-in {
                animation: fadeIn 0.5s ease-out;
            }
            
            .animate-fade-in-up {
                animation: fadeInUp 0.8s ease-out;
            }
            
            .animate-pulse-slow {
                animation: pulseSlow 2s ease-in-out infinite;
            }
            
            .animate-border-pulse {
                animation: borderPulse 3s ease-in-out infinite;
            }
            
            .animate-border-glow {
                animation: borderGlow 2s ease-in-out infinite;
            }
            
            .animate-spin-slow {
                animation: spinSlow 3s linear infinite;
            }
            
            .animate-gradient {
                background-size: 200% 200%;
                animation: gradient 3s ease infinite;
            }
            
            .animate-slide-in {
                animation: slideIn 0.3s ease-out;
            }
        </style>
    </body>
</html>
