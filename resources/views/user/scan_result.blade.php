<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
        <title>Hasil Analisis</title>
        <meta name="csrf-token" content="{{ csrf_token() }}" />
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
    <body class="bg-stone-200 font-sans text-stone-800" id="scanResultPage">
        <!-- Header -->
        <header class="sticky top-0 z-30 bg-pink-200/90 backdrop-blur border-b border-pink-300/40 transition-all duration-300 shadow-sm">
            <div class="mx-auto max-w-screen-md px-3 sm:px-4 py-2.5 sm:py-3 flex items-center gap-2 sm:gap-3">
                <a href="{{ route('scan.camera') }}" class="grid h-8 w-8 sm:h-9 sm:w-9 place-items-center rounded-lg border border-stone-300 bg-pink-100/60 text-stone-700 touch-manipulation transition-all duration-300 hover:bg-pink-200 hover:scale-110 hover:shadow-md group" aria-label="Kembali">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 sm:h-5 sm:w-5 transition-transform duration-300 group-hover:-translate-x-1"><path d="M15 6l-6 6 6 6" stroke-width="1.5"/></svg>
                </a>
                <h1 class="text-base sm:text-lg font-semibold bg-gradient-to-r from-pink-600 to-amber-600 bg-clip-text text-transparent animate-gradient">Hasil Analisis</h1>
            </div>
        </header>

        <main class="mx-auto max-w-screen-md px-3 sm:px-4 pb-6 sm:pb-8">
            <section class="mt-4 sm:mt-6 animate-fade-in-up">
                <!-- Header Card -->
                <div class="rounded-2xl bg-gradient-to-br from-pink-100 via-pink-50 to-amber-100 px-4 sm:px-6 py-4 sm:py-5 text-center shadow-lg border border-pink-200/50">
                    <div class="flex items-center justify-center gap-2 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 sm:h-6 sm:w-6 text-pink-600">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <p class="font-bold text-base sm:text-lg text-pink-900">Hasil Analisis Dipersonalisasi</p>
                    </div>
                    <p class="text-xs sm:text-sm text-stone-700 leading-relaxed">Rekomendasi gaya rambut terbaik berdasarkan foto, bentuk wajah, dan preferensi Anda.</p>
                </div>

                <!-- Photo & Face Shape Section -->
                <div class="mt-4 sm:mt-6 flex flex-col items-center gap-4 sm:gap-5">
                    <!-- Preview hasil capture (SERVER-SIDE) -->
                    @if(isset($scanImageUrl) && $scanImageUrl)
                    <div class="relative animate-scale-in">
                        <div class="absolute -inset-2 bg-gradient-to-r from-pink-400 to-amber-400 rounded-2xl blur-lg opacity-30"></div>
                        <div class="relative">
                            <img src="{{ $scanImageUrl }}" alt="Hasil capture" class="h-40 w-40 sm:h-52 sm:w-52 rounded-2xl object-cover shadow-2xl ring-4 ring-white transition-all duration-300 hover:scale-105" />
                            <div class="absolute -bottom-2 -right-2 bg-pink-500 text-white rounded-full p-2 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4 sm:h-5 sm:w-5">
                                    <path d="M4.5 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM14.25 8.625a3.375 3.375 0 116.75 0 3.375 3.375 0 01-6.75 0zM1.5 19.125a7.125 7.125 0 0114.25 0v.004l-.004.225a9.646 9.646 0 01-1.857 5.07 2.978 2.978 0 01-2.87 1.702c-.896 0-1.7-.393-2.27-1.016a9.6 9.6 0 01-1.857-5.071v-.228zM17.25 19.125l-.001.144a2.25 2.25 0 01-.233.96 10.088 10.088 0 005.06-1.01.75.75 0 00.42-.643 4.875 4.875 0 00-6.957-4.611 8.586 8.586 0 011.71 5.157v.003z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- ✅ FALLBACK: Preview dari sessionStorage (CLIENT-SIDE) -->
                    <div id="capturePreview" class="relative animate-scale-in hidden">
                        <div class="absolute -inset-2 bg-gradient-to-r from-pink-400 to-amber-400 rounded-2xl blur-lg opacity-30"></div>
                        <div class="relative">
                            <img id="captureImage" src="" alt="Hasil capture" class="h-40 w-40 sm:h-52 sm:w-52 rounded-2xl object-cover shadow-2xl ring-4 ring-white transition-all duration-300 hover:scale-105" />
                            <div class="absolute -bottom-2 -right-2 bg-pink-500 text-white rounded-full p-2 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4 sm:h-5 sm:w-5">
                                    <path d="M4.5 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM14.25 8.625a3.375 3.375 0 116.75 0 3.375 3.375 0 01-6.75 0zM1.5 19.125a7.125 7.125 0 0114.25 0v.004l-.004.225a9.646 9.646 0 01-1.857 5.07 2.978 2.978 0 01-2.87 1.702c-.896 0-1.7-.393-2.27-1.016a9.6 9.6 0 01-1.857-5.071v-.228zM17.25 19.125l-.001.144a2.25 2.25 0 01-.233.96 10.088 10.088 0 005.06-1.01.75.75 0 00.42-.643 4.875 4.875 0 00-6.957-4.611 8.586 8.586 0 011.71 5.157v.003z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Badge Bentuk Wajah (SERVER-SIDE) -->
                    @if(isset($faceShape) && $faceShape)
                    <div class="inline-flex items-center gap-2 sm:gap-2.5 rounded-full bg-gradient-to-r from-pink-500 to-amber-500 px-4 sm:px-5 py-2 sm:py-2.5 text-white shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4 sm:h-5 sm:w-5">
                            <path fill-rule="evenodd" d="M9 4.5a.75.75 0 01.721.544l.813 2.846a3.75 3.75 0 002.576 2.576l2.846.813a.75.75 0 010 1.442l-2.846.813a3.75 3.75 0 00-2.576 2.576l-.813 2.846a.75.75 0 01-1.442 0l-.813-2.846a3.75 3.75 0 00-2.576-2.576l-2.846-.813a.75.75 0 010-1.442l2.846-.813A3.75 3.75 0 007.466 7.89l.813-2.846A.75.75 0 019 4.5zM18 1.5a.75.75 0 01.728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 010 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 01-1.456 0l-.258-1.036a2.625 2.625 0 00-1.91-1.91l-1.036-.258a.75.75 0 010-1.456l1.036-.258a2.625 2.625 0 001.91-1.91l.258-1.036A.75.75 0 0118 1.5zM16.5 15a.75.75 0 01.712.513l.394 1.183c.15.447.5.799.948.948l1.183.395a.75.75 0 010 1.422l-1.183.395a1.5 1.5 0 00-.948.948l-.395 1.183a.75.75 0 01-1.422 0l-.395-1.183a1.5 1.5 0 00-.948-.948l-1.183-.395a.75.75 0 010-1.422l1.183-.395a1.5 1.5 0 00.948-.948l.395-1.183A.75.75 0 0116.5 15z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-xs sm:text-sm font-semibold">Bentuk Wajah:</span>
                        <span class="text-xs sm:text-sm font-bold">{{ ucfirst($faceShape) }}</span>
                    </div>
                    @endif
                </div>

                <!-- Section Title -->
                <div class="mt-6 sm:mt-8 mb-4 sm:mb-5">
                    <div class="flex items-center gap-3">
                        <div class="h-px flex-1 bg-gradient-to-r from-transparent via-pink-300 to-transparent"></div>
                        <h3 class="text-sm sm:text-base font-bold bg-gradient-to-r from-pink-600 to-amber-600 bg-clip-text text-transparent">Rekomendasi untuk Anda</h3>
                        <div class="h-px flex-1 bg-gradient-to-r from-transparent via-pink-300 to-transparent"></div>
                    </div>
                </div>
                
                <!-- Loading (Hanya tampil jika JS aktif dan belum ada rekomendasi) -->
                <div id="loadingAnalysis" class="mt-4 sm:mt-6 rounded-2xl bg-gradient-to-r from-pink-50 to-amber-50 border border-pink-200 px-6 py-8 text-center" style="display: none;">
                    <div class="flex flex-col items-center gap-3">
                        <svg class="animate-spin h-8 w-8 text-pink-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-sm sm:text-base font-medium text-stone-700">Sedang menganalisis foto Anda…</p>
                        <p class="text-xs text-stone-500">Mohon tunggu sebentar</p>
                    </div>
                </div>
                
                <!-- Rekomendasi SERVER-SIDE (TANPA JS) -->
                <!-- ✅ Element untuk JS analyze() - WAJIB ADA -->
                <div id="recommendations" class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                @if(isset($recommendations) && $recommendations->count() > 0)
                    @foreach($recommendations as $index => $model)
                    <div class="group relative rounded-2xl bg-white overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 animate-fade-in-up" style="animation-delay: {{ $index * 0.1 }}s">
                        <!-- Image Container -->
                        <div class="relative aspect-[3/4] overflow-hidden bg-gradient-to-br from-stone-100 to-stone-200">
                            <img 
                                src="{{ asset($model->image ?? 'img/model1.png') }}" 
                                alt="{{ $model->name }}"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                                loading="lazy"
                            />
                            <!-- Overlay Gradient -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/0 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <!-- Badge AI Recommended -->
                            @if(isset($aiEnabled) && $aiEnabled)
                            <div class="absolute top-3 right-3 bg-gradient-to-r from-pink-500 to-rose-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-lg border border-white/30">
                                ✨ AI Recommended
                            </div>
                            @else
                            <div class="absolute top-3 right-3 bg-pink-500 text-white text-[10px] font-semibold px-2 py-1 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                ✨ Recommended
                            </div>
                            @endif
                        </div>
                        <!-- Content -->
                        <div class="p-4 sm:p-5">
                            <h4 class="text-sm sm:text-base font-bold text-stone-900 line-clamp-2 mb-2 group-hover:text-pink-600 transition-colors">{{ $model->name }}</h4>
                            <div class="space-y-1.5">
                                @if($model->types)
                                <div class="flex items-center gap-2 text-xs sm:text-sm text-stone-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-3.5 w-3.5 text-pink-500">
                                        <path d="M12 2L2 7l10 5 10-5-10-5z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M2 17l10 5 10-5M2 12l10 5 10-5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span>{{ $model->types }}</span>
                                </div>
                                @endif
                                @if($model->length)
                                <div class="flex items-center gap-2 text-xs sm:text-sm text-stone-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-3.5 w-3.5 text-pink-500">
                                        <path d="M12 2v20M2 12h20" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span>{{ $model->length }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        <!-- Hover Effect Border -->
                        <div class="absolute inset-0 rounded-2xl border-2 border-pink-400/0 group-hover:border-pink-400/50 transition-all duration-300 pointer-events-none"></div>
                    </div>
                    @endforeach
                @else
                <!-- Fallback jika tidak ada rekomendasi (TANPA DEFAULT OVAL) -->
                <div class="col-span-full rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-200 px-6 py-8 text-center shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-12 w-12 mx-auto text-amber-500 mb-3">
                        <path d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p class="text-sm sm:text-base text-amber-900 font-semibold mb-1">Tidak ada rekomendasi tersedia</p>
                    <p class="text-xs sm:text-sm text-amber-700">Silakan coba scan ulang atau hubungi admin.</p>
                </div>
                @endif
                </div>
                <!-- ✅ END recommendations element -->
                <style>
                    /* Sembunyikan loading jika rekomendasi sudah di-render server-side */
                    @if(isset($recommendations) && $recommendations->count() > 0)
                    #loadingAnalysis {
                        display: none;
                    }
                    @endif
                </style>
               
            </section>
        </main>

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
                0%, 100% { opacity: 1; }
                50% { opacity: 0.8; }
            }
            
            @keyframes gradient {
                0%, 100% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
            }
            
            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateX(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            @keyframes scaleIn {
                from {
                    opacity: 0;
                    transform: scale(0.9);
                }
                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }
            
            @keyframes shimmer {
                0% {
                    background-position: -1000px 0;
                }
                100% {
                    background-position: 1000px 0;
                }
            }
            
            .animate-fade-in {
                animation: fadeIn 0.6s ease-out;
            }
            
            .animate-fade-in-up {
                animation: fadeInUp 0.8s ease-out;
            }
            
            .animate-pulse-slow {
                animation: pulseSlow 2s ease-in-out infinite;
            }
            
            .animate-gradient {
                background-size: 200% 200%;
                animation: gradient 3s ease infinite;
            }
            
            .animate-slide-in {
                animation: slideIn 0.5s ease-out;
            }
            
            .animate-scale-in {
                animation: scaleIn 0.4s ease-out;
            }
            
            .animate-shimmer {
                animation: shimmer 2s infinite;
                background: linear-gradient(to right, #fce7f3 0%, #fdf2f8 50%, #fce7f3 100%);
                background-size: 1000px 100%;
            }
            
            /* Stagger animation for cards */
            @keyframes fadeInUpStagger {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            /* Smooth transitions */
            * {
                transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            /* Range input styling */
            input[type="range"] {
                -webkit-appearance: none;
                appearance: none;
                height: 6px;
                border-radius: 3px;
                background: #e7e5e4;
                outline: none;
            }
            
            input[type="range"]::-webkit-slider-thumb {
                -webkit-appearance: none;
                appearance: none;
                width: 18px;
                height: 18px;
                border-radius: 50%;
                background: #ec4899;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            }
            
            input[type="range"]::-webkit-slider-thumb:hover {
                background: #db2777;
                transform: scale(1.2);
                box-shadow: 0 4px 8px rgba(236, 72, 153, 0.4);
            }
            
            input[type="range"]::-moz-range-thumb {
                width: 18px;
                height: 18px;
                border-radius: 50%;
                background: #ec4899;
                cursor: pointer;
                border: none;
                transition: all 0.3s ease;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            }
            
            input[type="range"]::-moz-range-thumb:hover {
                background: #db2777;
                transform: scale(1.2);
                box-shadow: 0 4px 8px rgba(236, 72, 153, 0.4);
            }
        </style>
    </body>
</html>