<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
        <title>Trendy Salon - Scan</title>
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
            };
        </script>
        <script src="{{ asset('build/assets/app-CWlvN2px.js') }}" defer></script>
    </head>
    <body class="bg-stone-200 font-sans text-stone-800">
        <!-- Header / Navigation -->
        <header class="sticky top-0 z-30 bg-pink-200/90 backdrop-blur border-b border-pink-300/40 transition-all duration-300 shadow-sm">
            <div class="mx-auto max-w-screen-md px-3 sm:px-4 py-2.5 sm:py-3 flex items-center gap-2 sm:gap-3">
                <a href="{{ route('user.home') }}" class="flex items-center gap-2 sm:gap-3 group">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo Trendy Salon" class="h-8 sm:h-10 w-auto rounded-sm shadow transition-all duration-300 group-hover:opacity-90 group-hover:scale-105 group-hover:shadow-lg" />
                    <div class="leading-tight animate-fade-in">
                        <p class="text-[10px] sm:text-xs tracking-widest text-stone-600">Trendy Salon</p>
                        <p class="text-xs sm:text-sm font-medium">TrendyLook</p>
                    </div>
                </a>

                <!-- Desktop Nav -->
             

            </div>
          
        </header>

        <main class="mx-auto max-w-screen-md px-3 sm:px-4 pb-6 sm:pb-8">
            <!-- Scan Form -->
            <section class="mt-4 sm:mt-6 rounded-2xl bg-white px-4 sm:px-6 py-5 sm:py-7 shadow-xl border border-stone-200/50 transition-all duration-300 animate-fade-in-up">
                <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-5">
                    <a href="{{ route('user.home') }}" class="grid h-8 w-8 sm:h-9 sm:w-9 place-items-center rounded-lg border border-stone-300 bg-pink-100/60 text-stone-700 touch-manipulation transition-all duration-300 hover:bg-pink-200 hover:scale-110 hover:shadow-md group" aria-label="Kembali">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 sm:h-5 sm:w-5 transition-transform duration-300 group-hover:-translate-x-1"><path d="M15 6l-6 6 6 6" stroke-width="1.5"/></svg>
                    </a>
                    <h2 class="text-base sm:text-lg font-bold bg-gradient-to-r from-pink-600 to-amber-600 bg-clip-text text-transparent animate-gradient">Informasi Awal</h2>
                </div>
                
                <!-- Header Card -->
                <div class="rounded-2xl bg-gradient-to-br from-pink-100 via-pink-50 to-amber-100 px-4 sm:px-5 py-4 sm:py-5 text-center shadow-lg border border-pink-200/50 mb-5 sm:mb-6">
                    <div class="flex items-center justify-center gap-2 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 sm:h-6 sm:w-6 text-pink-600">
                            <path d="M12 2L2 7l10 5 10-5-10-5z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 17l10 5 10-5M2 12l10 5 10-5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <p class="font-bold text-base sm:text-lg text-pink-900">Ceritakan Tentang Rambut Anda</p>
                    </div>
                    <p class="text-xs sm:text-sm text-stone-700 leading-relaxed">Jawab beberapa pertanyaan untuk hasil rekomendasi yang lebih akurat</p>
                </div>

                <form id="scanForm" class="space-y-4 sm:space-y-5" novalidate>
                    <div class="rounded-2xl border-2 border-stone-200 bg-gradient-to-br from-stone-50 to-white p-4 sm:p-5 shadow-md hover:shadow-lg hover:border-pink-300 transition-all duration-300 group animate-fade-in-up">
                        <label class="text-sm sm:text-base font-bold flex items-center gap-2 mb-3 text-stone-800">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 text-pink-500">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="7" r="4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Nama</span>
                            <span class="text-pink-600">*</span>
                        </label>
                        <input id="nameInput" name="name" type="text" placeholder="Masukkan nama Anda" class="w-full rounded-xl border-2 border-stone-300 bg-white px-4 py-3 text-sm sm:text-base outline-none transition-all duration-300 focus:border-pink-400 focus:ring-2 focus:ring-pink-200 group-hover:border-pink-300" required />
                    </div>
                    
                    <div class="rounded-2xl border-2 border-stone-200 bg-gradient-to-br from-stone-50 to-white p-4 sm:p-5 shadow-md hover:shadow-lg hover:border-pink-300 transition-all duration-300 group animate-fade-in-up">
                        <label class="text-sm sm:text-base font-bold flex items-center gap-2 mb-3 text-stone-800">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 text-pink-500">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Nomor Ponsel</span>
                            <span class="text-pink-600">*</span>
                        </label>
                        <input id="phoneInput" name="phone" type="tel" placeholder="Masukkan nomor ponsel" class="w-full rounded-xl border-2 border-stone-300 bg-white px-4 py-3 text-sm sm:text-base outline-none transition-all duration-300 focus:border-pink-400 focus:ring-2 focus:ring-pink-200 group-hover:border-pink-300" required />
                    </div>
                    
                    <div class="rounded-2xl border-2 border-stone-200 bg-gradient-to-br from-stone-50 to-white p-4 sm:p-5 shadow-md hover:shadow-lg hover:border-pink-300 transition-all duration-300 group animate-fade-in-up">
                        <label class="text-sm sm:text-base font-bold flex items-center gap-2 mb-3 text-stone-800">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 text-pink-500">
                                <path d="M12 2v20M2 12h20" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Model Panjang Rambut</span>
                            <span class="text-pink-600">*</span>
                        </label>
                        <select id="lengthSelect" name="length" class="w-full rounded-xl border-2 border-stone-300 bg-white px-4 py-3 text-sm sm:text-base outline-none transition-all duration-300 focus:border-pink-400 focus:ring-2 focus:ring-pink-200 group-hover:border-pink-300 cursor-pointer" required>
                            <option value="" selected>Pilih Model Panjang Rambut</option>
                            @foreach(($lengths ?? []) as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="rounded-2xl border-2 border-stone-200 bg-gradient-to-br from-stone-50 to-white p-4 sm:p-5 shadow-md hover:shadow-lg hover:border-pink-300 transition-all duration-300 group animate-fade-in-up">
                        <label class="text-sm sm:text-base font-bold flex items-center gap-2 mb-3 text-stone-800">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 text-pink-500">
                                <path d="M12 2L2 7l10 5 10-5-10-5z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M2 17l10 5 10-5M2 12l10 5 10-5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Jenis Rambut</span>
                            <span class="text-pink-600">*</span>
                        </label>
                        <select id="typeSelect" name="type" class="w-full rounded-xl border-2 border-stone-300 bg-white px-4 py-3 text-sm sm:text-base outline-none transition-all duration-300 focus:border-pink-400 focus:ring-2 focus:ring-pink-200 group-hover:border-pink-300 cursor-pointer" required>
                            <option value="" selected>Pilih Jenis Rambut</option>
                            @foreach(($types ?? []) as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="rounded-2xl border-2 border-stone-200 bg-gradient-to-br from-stone-50 to-white p-4 sm:p-5 shadow-md hover:shadow-lg hover:border-pink-300 transition-all duration-300 group animate-fade-in-up">
                        <label class="text-sm sm:text-base font-bold flex items-center gap-2 mb-3 text-stone-800">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 text-pink-500">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Tipe Rambut</span>
                            <span class="text-pink-600">*</span>
                        </label>
                        <select id="conditionSelect" name="condition" class="w-full rounded-xl border-2 border-stone-300 bg-white px-4 py-3 text-sm sm:text-base outline-none transition-all duration-300 focus:border-pink-400 focus:ring-2 focus:ring-pink-200 group-hover:border-pink-300 cursor-pointer" required>
                            <option value="" selected>Pilih Tipe Rambut</option>
                            @foreach(($conditions ?? []) as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-6 sm:mt-8 flex justify-center">
                        <button id="scanSubmit" data-target="{{ route('scan.camera') }}" type="button" class="group relative inline-flex items-center gap-3 rounded-2xl bg-gradient-to-r from-pink-500 via-pink-500 to-rose-500 px-8 sm:px-10 py-4 sm:py-4 text-sm sm:text-base font-bold text-white shadow-xl hover:shadow-2xl hover:from-pink-600 hover:via-pink-600 hover:to-rose-600 active:scale-95 touch-manipulation transition-all duration-300 overflow-hidden">
                            <!-- Shimmer effect -->
                            <span class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 bg-gradient-to-r from-transparent via-white/30 to-transparent"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 relative z-10">
                                <path d="M4 7h3l2-2h6l2 2h3a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2Z" stroke-width="2"/>
                                <circle cx="12" cy="13" r="3" stroke-width="2"/>
                            </svg>
                            <span class="relative z-10">Lanjutkan Scan</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 relative z-10 transition-transform duration-300 group-hover:translate-x-1">
                                <path d="M5 12h14M12 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </section>

            <footer class="mt-8 sm:mt-10 pb-6 sm:pb-8 text-center text-xs sm:text-sm text-stone-500 animate-fade-in">
                &copy; {{ date('Y') }} Trendy Salon. All rights reserved.
            </footer>
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
            
            .animate-fade-in {
                animation: fadeIn 0.6s ease-out;
            }
            
            .animate-fade-in-up {
                animation: fadeInUp 0.8s ease-out;
            }
            
            .animate-pulse-slow {
                animation: pulseSlow 3s ease-in-out infinite;
            }
            
            .animate-gradient {
                background-size: 200% 200%;
                animation: gradient 3s ease infinite;
            }
            
            /* Form input focus effects */
            input:focus, select:focus {
                transform: scale(1.01);
            }
            
            /* Smooth transitions */
            * {
                transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            /* Stagger animation for form fields */
            .animate-fade-in-up:nth-child(1) { animation-delay: 0.1s; }
            .animate-fade-in-up:nth-child(2) { animation-delay: 0.2s; }
            .animate-fade-in-up:nth-child(3) { animation-delay: 0.3s; }
            .animate-fade-in-up:nth-child(4) { animation-delay: 0.4s; }
            .animate-fade-in-up:nth-child(5) { animation-delay: 0.5s; }
        </style>
    </body>
</html>