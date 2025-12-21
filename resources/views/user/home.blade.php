<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
        <title>Trendy Salon - Home</title>
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
        <script src="{{ asset('build/assets/app-CWtpYyOp.js') }}" defer></script>
    </head>
    <body class="bg-stone-200 font-sans text-stone-800">
        <!-- Header -->
        <header class="sticky top-0 z-30 bg-pink-200/90 backdrop-blur transition-all duration-300 shadow-sm">
            <div class="mx-auto max-w-screen-md px-3 sm:px-4 py-2.5 sm:py-3 flex items-center gap-2 sm:gap-3">
                <a href="{{ route('admin.login') }}" class="inline-flex items-center group" title="Login Admin">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo Trendy Salon" class="h-8 sm:h-10 w-auto rounded-sm shadow transition-all duration-300 group-hover:opacity-90 group-hover:scale-105 group-hover:shadow-lg" />
                </a>
                <div class="leading-tight animate-fade-in">
                    <p class="text-[10px] sm:text-xs tracking-widest text-stone-600">Trendy Salon</p>
                    <p class="text-xs sm:text-sm font-medium">Layanan Terbaik Kami</p>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-screen-md px-3 sm:px-4 pb-6 sm:pb-8">
            <!-- Hero Slider -->
            <section class="mt-4 sm:mt-6 animate-fade-in-up">
                <div id="heroSlider" class="relative overflow-hidden rounded-2xl shadow-2xl group border-2 border-stone-300/50">
                    <!-- Slides -->
                    <div class="slide">
                        <img src="{{ asset('img/home.jpg') }}" alt="Perawatan Rambut" class="w-full h-52 sm:h-72 md:h-96 object-cover transition-transform duration-700 group-hover:scale-110" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                        <div class="absolute bottom-6 sm:bottom-8 left-5 sm:left-8 right-5 sm:right-8 text-white animate-slide-up">
                            <p class="text-xl sm:text-3xl md:text-4xl font-extrabold drop-shadow-2xl mb-1">Rasakan Perawatan Premium</p>
                            <p class="text-lg sm:text-2xl md:text-3xl font-bold mt-1 drop-shadow-xl">Dengan Sentuhan Profesional</p>
                            <p class="mt-2 sm:mt-3 text-xs sm:text-sm md:text-base tracking-widest drop-shadow-lg font-semibold">DI TRENDY SALON</p>
                        </div>
                        <div class="absolute top-4 sm:top-6 right-4 sm:right-6 animate-pulse-slow">
                            <div class="h-14 w-14 sm:h-20 sm:w-20 md:h-24 md:w-24 rounded-full bg-gradient-to-br from-white/90 to-white/70 grid place-items-center text-[9px] sm:text-[11px] md:text-xs font-bold text-pink-700 leading-tight shadow-2xl transition-all duration-300 hover:scale-110 hover:from-white hover:to-pink-50 border-2 border-pink-200/50">
                                STAY<br/>SAFE &<br/>HEALTHY
                            </div>
                        </div>
                    </div>
                    <!-- Single image, no controls -->
                </div>
            </section>

            <!-- Intro & CTA -->
            <section class="mt-5 sm:mt-7 rounded-2xl bg-gradient-to-br from-pink-50 via-amber-50 to-pink-50 px-5 sm:px-7 py-6 sm:py-8 shadow-xl border-2 border-pink-200/50 hover:shadow-2xl transition-all duration-300 animate-fade-in-up">
                <div class="text-center">
                    <div class="flex items-center justify-center gap-2 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-6 w-6 sm:h-8 sm:w-8 text-pink-600">
                            <path d="M12 2L2 7l10 5 10-5-10-5z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 17l10 5 10-5M2 12l10 5 10-5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <h2 class="text-xl sm:text-2xl md:text-3xl font-extrabold uppercase tracking-wide leading-tight bg-gradient-to-r from-pink-600 to-amber-600 bg-clip-text text-transparent animate-gradient">
                            TEMUKAN MODEL POTONGAN RAMBUT TERBAIK ANDA!
                        </h2>
                    </div>
                    <div class="mt-3 flex items-center justify-center gap-3 sm:gap-4">
                        <span class="h-1 w-20 sm:w-32 bg-gradient-to-r from-transparent via-pink-400 to-pink-500 rounded-full"></span>
                        <span class="text-2xl sm:text-3xl animate-bounce-slow">✨</span>
                        <span class="h-1 w-20 sm:w-32 bg-gradient-to-r from-pink-500 via-pink-400 to-transparent rounded-full"></span>
                    </div>
                    <p class="mt-4 sm:mt-5 text-center text-sm sm:text-base text-stone-800 leading-relaxed px-2 font-medium">Biarkan <span class="inline-block rounded-lg bg-gradient-to-r from-pink-400 to-rose-400 px-3 py-1 font-bold text-white shadow-lg animate-pulse-slow">TrendyLook</span> menganalisis<br class="sm:hidden"/>Bentuk Wajahmu Dan Temukan Gaya<br class="sm:hidden"/>Rambut Terbaik Untuk Tampil Lebih Percaya Diri!</p>
                </div>
                <div class="mt-6 sm:mt-7 flex justify-center">
                    <a href="{{ route('scan') }}" class="group inline-flex items-center gap-3 rounded-2xl bg-gradient-to-r from-pink-500 via-pink-500 to-rose-500 px-8 sm:px-10 py-4 sm:py-4 text-sm sm:text-base font-bold text-white shadow-2xl hover:shadow-3xl hover:from-pink-600 hover:via-pink-600 hover:to-rose-600 active:scale-95 transition-all duration-300 touch-manipulation relative overflow-hidden">
                        <!-- Shimmer effect -->
                        <span class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 bg-gradient-to-r from-transparent via-white/30 to-transparent"></span>
                        <!-- Icon kamera -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 sm:h-6 sm:w-6 relative z-10">
                            <path d="M4 7h3l2-2h6l2 2h3a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2Z" stroke-width="2"/>
                            <circle cx="12" cy="13" r="3" stroke-width="2"/>
                        </svg>
                        <span class="whitespace-nowrap relative z-10">Mulai Scan Sekarang</span>
                        <!-- Arrow kanan -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 sm:h-6 sm:w-6 relative z-10 transition-transform duration-300 group-hover:translate-x-1">
                            <path d="M5 12h14M12 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            </section>

            <!-- Visit Section -->
            <section class="mt-6 sm:mt-8 animate-fade-in-up">
                <div class="text-center mb-5 sm:mb-6">
                    <div class="flex items-center justify-center gap-2 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 sm:h-6 sm:w-6 text-pink-600">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="10" r="3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-stone-800">Kunjungi Trendy Salon Terdekat Anda Sekarang</h3>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6 flex justify-center">
                    <div class="relative group">
                        <div class="absolute -inset-2 bg-gradient-to-r from-pink-400 to-amber-400 rounded-full blur-lg opacity-30 group-hover:opacity-50 transition-opacity duration-300"></div>
                        <div class="relative h-36 w-36 sm:h-48 sm:w-48 overflow-hidden rounded-full shadow-2xl ring-4 ring-white transition-transform duration-500 group-hover:scale-110">
                            <img src="{{ asset('img/home2.jpg') }}" alt="Salon" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-125"/>
                        </div>
                        <!-- Badge di luar lingkaran -->
                        <div class="absolute -right-3 sm:-right-5 top-3 sm:top-5 animate-float">
                            <div class="h-16 w-16 sm:h-22 sm:w-22 rounded-full bg-gradient-to-br from-amber-400 to-amber-500 shadow-2xl grid place-items-center text-center text-[9px] sm:text-[11px] font-bold text-white tracking-wide leading-tight px-1.5 transition-all duration-300 hover:scale-110 hover:shadow-3xl hover:from-amber-500 hover:to-amber-600 border-2 border-white">
                                BUKA<br/>SETIAP HARI<br/>
                                <span class="font-extrabold">08:00-20:00</span>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="mx-auto mt-5 sm:mt-6 max-w-sm rounded-2xl bg-gradient-to-r from-pink-100 to-amber-100 px-4 sm:px-5 py-3 sm:py-4 text-center text-xs sm:text-sm font-bold text-stone-800 shadow-lg border-2 border-pink-200/50 transition-all duration-300 hover:shadow-xl hover:from-pink-200 hover:to-amber-200">
                    *DISARANKAN <span class="text-pink-600">RESERVASI</span> TERLEBIH DAHULU
                </p>
            </section>

            <!-- Branches with dropdown to show website/map -->
            <section class="mt-6 sm:mt-8 space-y-4 sm:space-y-5">
                <!-- Giwangan -->
                <div class="rounded-2xl bg-white shadow-lg hover:shadow-2xl transition-all duration-300 animate-fade-in-up group border-2 border-stone-200/50 overflow-hidden">
                    <button type="button" class="branch-toggle flex w-full items-start gap-3 sm:gap-4 px-4 sm:px-5 py-4 sm:py-5 touch-manipulation transition-colors duration-200 hover:bg-gradient-to-r hover:from-pink-50 hover:to-amber-50 rounded-t-2xl" data-target="#branch-giwangan">
                        <span class="grid h-7 w-7 sm:h-8 sm:w-8 place-items-center rounded-full bg-gradient-to-br from-pink-500 to-rose-500 text-white font-bold text-sm sm:text-base transition-all duration-300 flex-shrink-0 mt-0.5 group-hover:scale-110 group-hover:from-pink-600 group-hover:to-rose-600 shadow-lg">›</span>
                        <div class="text-left flex-1 min-w-0">
                            <p class="font-bold text-base sm:text-lg group-hover:text-pink-600 transition-colors duration-200">Cabang Giwangan</p>
                            <p class="text-sm sm:text-base text-stone-600 mt-1 leading-relaxed">Jl. Pramuka No39A, Prenggan, Kec. Kotagede, Kota Yogyakarta, DIY</p>
                        </div>
                    </button>
                    <div id="branch-giwangan" class="hidden border-t-2 border-pink-200/50 animate-slide-down bg-gradient-to-br from-stone-50 to-white">
                        <div class="px-4 sm:px-5 py-3 sm:py-4 text-xs sm:text-sm font-semibold text-pink-700 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="10" r="3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Website/Maps
                        </div>
                        <div class="px-4 sm:px-5 pb-4 sm:pb-5">
                            <div class="aspect-video w-full overflow-hidden rounded-xl border-2 border-stone-200 shadow-lg">
                                <iframe class="h-full w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3952.676347770811!2d110.390335!3d-7.824039600000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a573c8729ad31%3A0xcb31d1555a656237!2zVHJlbmR5IFNhbG9uIOqni-qmoOqmv-qmvOqmpOqngOqmneqngOqmquqngOqmseqmreqmuuqmtOqmpOqngA!5e0!3m2!1sid!2sid!4v1763528154812!5m2!1sid!2sid"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sapen -->
                <div class="rounded-2xl bg-white shadow-lg hover:shadow-2xl transition-all duration-300 animate-fade-in-up group border-2 border-stone-200/50 overflow-hidden">
                    <button type="button" class="branch-toggle flex w-full items-start gap-3 sm:gap-4 px-4 sm:px-5 py-4 sm:py-5 touch-manipulation transition-colors duration-200 hover:bg-gradient-to-r hover:from-pink-50 hover:to-amber-50 rounded-t-2xl" data-target="#branch-sapen">
                        <span class="grid h-7 w-7 sm:h-8 sm:w-8 place-items-center rounded-full bg-gradient-to-br from-pink-500 to-rose-500 text-white font-bold text-sm sm:text-base transition-all duration-300 flex-shrink-0 mt-0.5 group-hover:scale-110 group-hover:from-pink-600 group-hover:to-rose-600 shadow-lg">›</span>
                        <div class="text-left flex-1 min-w-0">
                            <p class="font-bold text-base sm:text-lg group-hover:text-pink-600 transition-colors duration-200">Cabang Sapen</p>
                            <p class="text-sm sm:text-base text-stone-600 mt-1 leading-relaxed">Jl. bimaskati No45 A, Demangan, Kec. Gondokusuman, Kota Yogyakarta, DIY</p>
                        </div>
                    </button>
                    <div id="branch-sapen" class="hidden border-t-2 border-pink-200/50 animate-slide-down bg-gradient-to-br from-stone-50 to-white">
                        <div class="px-4 sm:px-5 py-3 sm:py-4 text-xs sm:text-sm font-semibold text-pink-700 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="10" r="3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Website/Maps
                        </div>
                        <div class="px-4 sm:px-5 pb-4 sm:pb-5">
                            <div class="aspect-video w-full overflow-hidden rounded-xl border-2 border-stone-200 shadow-lg">
                                <iframe class="h-full w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.0147614538614!2d110.391307!3d-7.788259000000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a59da72a704e5%3A0x5b7cb4bc6028e8ab!2zQmUmWW91IFNhbG9uLyBUcmVuZHkgU2Fsb24o6qeL6qan6qa6JuqmquqmuuqmtOqmiOqmseqmreqmuuqmtOqmpOqngC_qpqDqpr_qprzqpqTqp4Dqpp3qp4Dqpqrqp4DqprHqpq3qprrqprTqpqTqp4Ap!5e0!3m2!1sid!2sid!4v1763528188007!5m2!1sid!2sid"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kotabaru -->
                <div class="rounded-2xl bg-white shadow-lg hover:shadow-2xl transition-all duration-300 animate-fade-in-up group border-2 border-stone-200/50 overflow-hidden">
                    <button type="button" class="branch-toggle flex w-full items-start gap-3 sm:gap-4 px-4 sm:px-5 py-4 sm:py-5 touch-manipulation transition-colors duration-200 hover:bg-gradient-to-r hover:from-pink-50 hover:to-amber-50 rounded-t-2xl" data-target="#branch-kotabaru">
                        <span class="grid h-7 w-7 sm:h-8 sm:w-8 place-items-center rounded-full bg-gradient-to-br from-pink-500 to-rose-500 text-white font-bold text-sm sm:text-base transition-all duration-300 flex-shrink-0 mt-0.5 group-hover:scale-110 group-hover:from-pink-600 group-hover:to-rose-600 shadow-lg">›</span>
                        <div class="text-left flex-1 min-w-0">
                            <p class="font-bold text-base sm:text-lg group-hover:text-pink-600 transition-colors duration-200">Cabang Kotabaru</p>
                            <p class="text-sm sm:text-base text-stone-600 mt-1 leading-relaxed">Jl. Kiasakti Timur No14, Kotabaru, Kec. Danurejan, Kota Yogyakarta, DIY</p>
                        </div>
                    </button>
                    <div id="branch-kotabaru" class="hidden border-t-2 border-pink-200/50 animate-slide-down bg-gradient-to-br from-stone-50 to-white">
                        <div class="px-4 sm:px-5 py-3 sm:py-4 text-xs sm:text-sm font-semibold text-pink-700 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="10" r="3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Website/Maps
                        </div>
                        <div class="px-4 sm:px-5 pb-4 sm:pb-5">
                            <div class="aspect-video w-full overflow-hidden rounded-xl border-2 border-stone-200 shadow-lg">
                                <iframe class="h-full w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.0096117877865!2d110.3760499!3d-7.7888047!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a582cf3caa41d%3A0xe997d35e6c4041d5!2sTrendy%20Salon!5e0!3m2!1sid!2sid!4v1763528212373!5m2!1sid!2sid"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seturan -->
                <div class="rounded-2xl bg-white shadow-lg hover:shadow-2xl transition-all duration-300 animate-fade-in-up group border-2 border-stone-200/50 overflow-hidden">
                    <button type="button" class="branch-toggle flex w-full items-start gap-3 sm:gap-4 px-4 sm:px-5 py-4 sm:py-5 touch-manipulation transition-colors duration-200 hover:bg-gradient-to-r hover:from-pink-50 hover:to-amber-50 rounded-t-2xl" data-target="#branch-seturan">
                        <span class="grid h-7 w-7 sm:h-8 sm:w-8 place-items-center rounded-full bg-gradient-to-br from-pink-500 to-rose-500 text-white font-bold text-sm sm:text-base transition-all duration-300 flex-shrink-0 mt-0.5 group-hover:scale-110 group-hover:from-pink-600 group-hover:to-rose-600 shadow-lg">›</span>
                        <div class="text-left flex-1 min-w-0">
                            <p class="font-bold text-base sm:text-lg group-hover:text-pink-600 transition-colors duration-200">Cabang Seturan</p>
                            <p class="text-sm sm:text-base text-stone-600 mt-1 leading-relaxed">Jl. Sekolan Mataram No430, Pringwulung, Condongcatur, Kec. Depok, Kabupaten Sleman, DIY</p>
                        </div>
                    </button>
                    <div id="branch-seturan" class="hidden border-t-2 border-pink-200/50 animate-slide-down bg-gradient-to-br from-stone-50 to-white">
                        <div class="px-4 sm:px-5 py-3 sm:py-4 text-xs sm:text-sm font-semibold text-pink-700 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="10" r="3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Website/Maps
                        </div>
                        <div class="px-4 sm:px-5 pb-4 sm:pb-5">
                            <div class="aspect-video w-full overflow-hidden rounded-xl border-2 border-stone-200 shadow-lg">
                                <iframe class="h-full w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.204400391403!2d110.39664289999999!3d-7.7681368!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a591b65a0040f%3A0x97bccaa03a2012a6!2sTrendy%20Salon%204!5e0!3m2!1sid!2sid!4v1763528256200!5m2!1sid!2sid"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <footer class="mt-8 sm:mt-10 pb-6 sm:pb-8 text-center text-xs sm:text-sm text-stone-500">
                &copy; {{ date('Y') }} Trendy Salon. All rights reserved.
            </footer>
        </main>

        <!-- Enhanced styles with animations -->
        <style>
            #heroSlider .slide { position: relative; }
            
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
            
            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            @keyframes slideDown {
                from {
                    opacity: 0;
                    max-height: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    max-height: 500px;
                    transform: translateY(0);
                }
            }
            
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
            }
            
            @keyframes pulseSlow {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.8; }
            }
            
            @keyframes bounceSlow {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-5px); }
            }
            
            @keyframes expand {
                from { width: 0; }
                to { width: 100%; }
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
            
            .animate-slide-up {
                animation: slideUp 1s ease-out 0.3s both;
            }
            
            .animate-slide-down {
                animation: slideDown 0.4s ease-out;
            }
            
            .animate-float {
                animation: float 3s ease-in-out infinite;
            }
            
            .animate-pulse-slow {
                animation: pulseSlow 3s ease-in-out infinite;
            }
            
            .animate-bounce-slow {
                animation: bounceSlow 2s ease-in-out infinite;
            }
            
            .animate-expand {
                animation: expand 1s ease-out;
            }
            
            .animate-gradient {
                background-size: 200% 200%;
                animation: gradient 3s ease infinite;
            }
            
            /* Smooth transitions for dropdown */
            #branch-giwangan, #branch-sapen, #branch-kotabaru, #branch-seturan {
                transition: all 0.4s ease-out;
                overflow: hidden;
            }
            
            /* Intersection Observer for scroll animations */
            .fade-in-on-scroll {
                opacity: 0;
                transform: translateY(30px);
                transition: opacity 0.6s ease-out, transform 0.6s ease-out;
            }
            
            .fade-in-on-scroll.visible {
                opacity: 1;
                transform: translateY(0);
            }
        </style>
        <script>
            // Enhanced dropdown toggles with smooth animations
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.branch-toggle').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const targetSel = btn.getAttribute('data-target');
                        const panel = targetSel ? document.querySelector(targetSel) : null;
                        if (!panel) return;
                        
                        const isHidden = panel.classList.contains('hidden');
                        if (isHidden) {
                            panel.classList.remove('hidden');
                            // Force reflow
                            panel.offsetHeight;
                            panel.style.opacity = '0';
                            panel.style.maxHeight = '0';
                            setTimeout(() => {
                                panel.style.transition = 'all 0.4s ease-out';
                                panel.style.opacity = '1';
                                panel.style.maxHeight = '500px';
                            }, 10);
                        } else {
                            panel.style.opacity = '0';
                            panel.style.maxHeight = '0';
                            setTimeout(() => {
                                panel.classList.add('hidden');
                            }, 400);
                        }
                        
                        // Rotate chevron with smooth transition
                        const chevron = btn.querySelector('span');
                        if (chevron) {
                            chevron.style.transition = 'transform 0.3s ease';
                            chevron.style.transform = isHidden ? 'rotate(90deg)' : 'rotate(0deg)';
                        }
                    });
                });
                
                // Intersection Observer for scroll animations
                const observerOptions = {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                };
                
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('visible');
                        }
                    });
                }, observerOptions);
                
                // Observe all sections
                document.querySelectorAll('section').forEach(section => {
                    section.classList.add('fade-in-on-scroll');
                    observer.observe(section);
                });
            });
        </script>
    </body>
</html>