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
        <!-- Inject absolute routes for JS to avoid base-path issues in production -->
        <script>
            window.__SCAN_ROUTES__ = {
                apiModels: "{{ url('/api/recommendations/hair-models') }}",
                analyze: "{{ url('/scan/analyze') }}"
            };
            window.__ASSET_BASE__ = "{{ url('/') }}";
        </script>
        <script src="{{ asset('build/assets/app-B8ho6jJ0.js') }}" defer></script>
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

        <main class="mx-auto max-w-screen-md px-3 sm:px-4">
            <section class="mt-3 sm:mt-4 animate-fade-in-up">
                <div class="rounded-xl bg-gradient-to-r from-pink-100 via-pink-50 to-amber-100 px-3 sm:px-4 py-3 sm:py-4 text-center shadow-md animate-pulse-slow">
                    <p class="font-semibold text-sm sm:text-base text-stone-800">Hasil Analisis Dipersonalisasi</p>
                    <p class="mt-1 text-[10px] sm:text-xs text-stone-700 leading-relaxed">Rekomendasi gaya rambut terbaik berdasarkan foto, bentuk wajah, dan preferensi Anda.</p>
                </div>

                <!-- Preview hasil capture -->
                <div id="capturePreview" class="mt-3 sm:mt-4 hidden animate-fade-in-up">
                    <div class="relative inline-block">
                        <img id="captureImage" alt="Hasil capture" class="mx-auto h-32 w-32 sm:h-44 sm:w-44 rounded-xl object-cover shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-2xl" />
                        <div class="absolute inset-0 rounded-xl border-2 border-pink-400/50 opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                </div>

                <h3 class="mt-5 sm:mt-6 text-xs sm:text-sm font-semibold bg-gradient-to-r from-pink-600 to-amber-600 bg-clip-text text-transparent animate-gradient">Rekomendasi untuk Anda</h3>
                <div id="loadingAnalysis" class="mt-2 text-[10px] sm:text-xs text-stone-600 flex items-center gap-2 animate-pulse-slow">
                    <svg class="animate-spin h-4 w-4 text-pink-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Sedang menganalisis foto Anda…</span>
                </div>
                <div id="recommendations" class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4"></div>
               
            </section>
        </main>

        <!-- Notification Toast Container -->
        <div id="notificationToast" class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 hidden transition-all duration-300">
            <div id="toastContainer" class="bg-white rounded-xl shadow-2xl border-2 border-stone-200 min-w-[300px] max-w-[90vw] px-4 py-3 animate-slide-in">
                <div class="flex items-start gap-3">
                    <div id="toastIcon" class="flex-shrink-0 mt-0.5"></div>
                    <div class="flex-1 min-w-0">
                        <p id="toastTitle" class="font-semibold text-sm text-stone-900"></p>
                        <p id="toastMessage" class="text-xs text-stone-600 mt-1 break-words"></p>
                    </div>
                    <button id="toastClose" class="flex-shrink-0 text-stone-400 hover:text-stone-600 transition-colors" aria-label="Tutup">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
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
            
            /* Recommendation card animations */
            #recommendations > div {
                animation: scaleIn 0.5s ease-out;
            }
            
            #recommendations > div:nth-child(1) { animation-delay: 0.1s; }
            #recommendations > div:nth-child(2) { animation-delay: 0.2s; }
            #recommendations > div:nth-child(3) { animation-delay: 0.3s; }
            #recommendations > div:nth-child(4) { animation-delay: 0.4s; }
            #recommendations > div:nth-child(5) { animation-delay: 0.5s; }
            #recommendations > div:nth-child(6) { animation-delay: 0.6s; }
            
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
        
        <script>
            // Enhanced recommendation card animations
            document.addEventListener('DOMContentLoaded', () => {
                // Observe when recommendations are added
                const recommendationsContainer = document.getElementById('recommendations');
                if (recommendationsContainer) {
                    const observer = new MutationObserver((mutations) => {
                        mutations.forEach((mutation) => {
                            mutation.addedNodes.forEach((node) => {
                                if (node.nodeType === 1 && node.classList.contains('rounded-xl')) {
                                    node.style.opacity = '0';
                                    node.style.transform = 'scale(0.9)';
                                    setTimeout(() => {
                                        node.style.transition = 'all 0.5s ease-out';
                                        node.style.opacity = '1';
                                        node.style.transform = 'scale(1)';
                                    }, 10);
                                }
                            });
                        });
                    });
                    
                    observer.observe(recommendationsContainer, { childList: true });
                }
                
            });
        </script>
    </body>
</html>