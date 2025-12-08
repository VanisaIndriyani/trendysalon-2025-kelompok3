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
        <script src="{{ asset('build/assets/app-CHUWRvb-.js') }}" defer></script>
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

        <main class="mx-auto max-w-screen-md px-3 sm:px-4">
            <!-- Scan Form -->
            <section class="mt-4 sm:mt-6 rounded-xl bg-stone-100 px-3 sm:px-4 py-4 sm:py-6 shadow-lg hover:shadow-xl transition-all duration-300 animate-fade-in-up">
                <div class="flex items-center gap-2 sm:gap-3">
                    <a href="{{ route('user.home') }}" class="grid h-8 w-8 sm:h-9 sm:w-9 place-items-center rounded-lg border border-stone-300 bg-pink-100/60 text-stone-700 touch-manipulation transition-all duration-300 hover:bg-pink-200 hover:scale-110 hover:shadow-md group" aria-label="Kembali">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 sm:h-5 sm:w-5 transition-transform duration-300 group-hover:-translate-x-1"><path d="M15 6l-6 6 6 6" stroke-width="1.5"/></svg>
                    </a>
                    <h2 class="text-base sm:text-lg font-semibold bg-gradient-to-r from-pink-600 to-amber-600 bg-clip-text text-transparent animate-gradient">Informasi Awal</h2>
                </div>
                <div class="mt-3 sm:mt-4 rounded-xl bg-gradient-to-r from-pink-100 via-pink-50 to-amber-100 px-3 sm:px-4 py-3 sm:py-4 shadow-md animate-pulse-slow">
                    <p class="text-center font-semibold text-sm sm:text-base text-stone-800">Ceritakan Tentang Rambut Anda</p>
                    <p class="mt-1 text-center text-[10px] sm:text-xs text-stone-700 leading-relaxed">jawab beberapa pertanyaan untuk hasil rekomendasi yang lebih akurat</p>
                </div>

                <form id="scanForm" class="mt-3 sm:mt-4 space-y-3 sm:space-y-4" novalidate>
                    <div class="rounded-xl border border-stone-200 bg-white p-3 sm:p-4 shadow-sm hover:shadow-md transition-all duration-300 group animate-fade-in-up">
                        <label class="text-xs sm:text-sm font-semibold flex items-center gap-1">
                            <span>Nama</span>
                            <span class="text-pink-600 animate-pulse-slow">*</span>
                        </label>
                        <input id="nameInput" name="name" type="text" placeholder="Nama" class="mt-2 w-full rounded-lg border border-stone-300 bg-stone-50 px-3 py-2.5 sm:py-2 text-sm sm:text-base outline-none transition-all duration-300 focus:border-pink-400 focus:ring-2 focus:ring-pink-200 focus:bg-white group-hover:border-pink-300" required />
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white p-3 sm:p-4 shadow-sm hover:shadow-md transition-all duration-300 group animate-fade-in-up">
                        <label class="text-xs sm:text-sm font-semibold flex items-center gap-1">
                            <span>Nomor Ponsel</span>
                            <span class="text-pink-600 animate-pulse-slow">*</span>
                        </label>
                        <input id="phoneInput" name="phone" type="tel" placeholder="Nomor Ponsel" class="mt-2 w-full rounded-lg border border-stone-300 bg-stone-50 px-3 py-2.5 sm:py-2 text-sm sm:text-base outline-none transition-all duration-300 focus:border-pink-400 focus:ring-2 focus:ring-pink-200 focus:bg-white group-hover:border-pink-300" required />
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white p-3 sm:p-4 shadow-sm hover:shadow-md transition-all duration-300 group animate-fade-in-up">
                        <label class="text-xs sm:text-sm font-semibold flex items-center gap-1">
                            <span>Model Panjang Rambut</span>
                            <span class="text-pink-600 animate-pulse-slow">*</span>
                        </label>
                        <select id="lengthSelect" name="length" class="mt-2 w-full rounded-lg border border-stone-300 bg-stone-50 px-3 py-2.5 sm:py-2 text-sm sm:text-base outline-none transition-all duration-300 focus:border-pink-400 focus:ring-2 focus:ring-pink-200 focus:bg-white group-hover:border-pink-300 cursor-pointer" required>
                            <option value="" selected>Pilih Model Panjang Rambut</option>
                            @foreach(($lengths ?? []) as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white p-3 sm:p-4 shadow-sm hover:shadow-md transition-all duration-300 group animate-fade-in-up">
                        <label class="text-xs sm:text-sm font-semibold flex items-center gap-1">
                            <span>Jenis Rambut</span>
                            <span class="text-pink-600 animate-pulse-slow">*</span>
                        </label>
                        <select id="typeSelect" name="type" class="mt-2 w-full rounded-lg border border-stone-300 bg-stone-50 px-3 py-2.5 sm:py-2 text-sm sm:text-base outline-none transition-all duration-300 focus:border-pink-400 focus:ring-2 focus:ring-pink-200 focus:bg-white group-hover:border-pink-300 cursor-pointer" required>
                            <option value="" selected>Pilih Jenis Rambut</option>
                            @foreach(($types ?? []) as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white p-3 sm:p-4 shadow-sm hover:shadow-md transition-all duration-300 group animate-fade-in-up">
                        <label class="text-xs sm:text-sm font-semibold flex items-center gap-1">
                            <span>Tipe Rambut</span>
                            <span class="text-pink-600 animate-pulse-slow">*</span>
                        </label>
                        <select id="conditionSelect" name="condition" class="mt-2 w-full rounded-lg border border-stone-300 bg-stone-50 px-3 py-2.5 sm:py-2 text-sm sm:text-base outline-none transition-all duration-300 focus:border-pink-400 focus:ring-2 focus:ring-pink-200 focus:bg-white group-hover:border-pink-300 cursor-pointer" required>
                            <option value="" selected>Pilih Tipe Rambut</option>
                            @foreach(($conditions ?? []) as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-5 sm:mt-6 flex justify-center">
                        <button id="scanSubmit" data-target="{{ route('scan.camera') }}" type="button" class="group relative inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-amber-200 to-amber-300 px-5 sm:px-6 py-2.5 sm:py-3 text-xs sm:text-sm font-semibold text-stone-900 shadow-md hover:shadow-xl hover:from-amber-300 hover:to-amber-400 active:translate-y-px touch-manipulation transition-all duration-300 overflow-hidden">
                            <!-- Shimmer effect -->
                            <span class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 bg-gradient-to-r from-transparent via-white/30 to-transparent"></span>
                            <span class="relative z-10">Lanjutkan Scan</span>
                            <span class="grid h-5 w-5 sm:h-6 sm:w-6 place-items-center rounded-full bg-amber-100 border border-stone-300 text-stone-700 text-sm sm:text-base transition-transform duration-300 group-hover:translate-x-1 group-hover:scale-110 relative z-10">›</span>
                        </button>
                    </div>
                </form>
            </section>

            <footer class="mt-8 sm:mt-10 pb-8 sm:pb-10 text-center text-[10px] sm:text-xs text-stone-500 animate-fade-in">
                &copy; {{ date('Y') }} Trendy Salon
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