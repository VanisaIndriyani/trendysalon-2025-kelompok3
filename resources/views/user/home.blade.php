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
        <script src="{{ asset('build/assets/app-B8ho6jJ0.js') }}" defer></script>
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

        <main class="mx-auto max-w-screen-md px-3 sm:px-4">
            <!-- Hero Slider -->
            <section class="mt-3 sm:mt-4 animate-fade-in-up">
                <div id="heroSlider" class="relative overflow-hidden rounded-xl shadow-lg group">
                    <!-- Slides -->
                    <div class="slide">
                        <img src="{{ asset('img/home.jpg') }}" alt="Perawatan Rambut" class="w-full h-48 sm:h-64 md:h-80 object-cover transition-transform duration-700 group-hover:scale-105" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                        <div class="absolute bottom-4 sm:bottom-6 left-4 sm:left-6 right-4 sm:right-6 text-white animate-slide-up">
                            <p class="text-lg sm:text-2xl md:text-3xl font-extrabold drop-shadow-lg">Rasakan Perawatan Premium</p>
                            <p class="text-base sm:text-xl md:text-2xl font-semibold mt-0.5 sm:mt-1 drop-shadow-md">Dengan Sentuhan Profesional</p>
                            <p class="mt-1.5 sm:mt-2 text-[10px] sm:text-xs md:text-sm tracking-widest drop-shadow">DI TRENDY SALON</p>
                        </div>
                        <div class="absolute top-3 sm:top-4 right-3 sm:right-4 animate-pulse-slow">
                            <div class="h-12 w-12 sm:h-16 sm:w-16 md:h-20 md:w-20 rounded-full bg-white/70 grid place-items-center text-[8px] sm:text-[10px] md:text-xs font-semibold text-stone-700 leading-tight shadow-lg transition-all duration-300 hover:scale-110 hover:bg-white/90">
                                STAY<br/>SAFE &<br/>HEALTHY
                            </div>
                        </div>
                    </div>
                    <!-- Single image, no controls -->
                </div>
            </section>

            <!-- Intro & CTA -->
            <section class="mt-4 sm:mt-6 rounded-xl bg-stone-100 px-3 sm:px-4 py-4 sm:py-6 shadow-lg hover:shadow-xl transition-all duration-300 animate-fade-in-up">
                <h2 class="text-center text-lg sm:text-xl md:text-2xl font-extrabold uppercase tracking-wide leading-tight bg-gradient-to-r from-pink-600 to-amber-600 bg-clip-text text-transparent animate-gradient">
                    TEMUKAN MODEL POTONGAN<br class="sm:hidden"/> RAMBUT TERBAIK ANDA!
                </h2>
                <div class="mt-2 flex items-center justify-center gap-3 sm:gap-4">
                    <span class="h-[2px] w-16 sm:w-24 bg-amber-300/70 animate-expand"></span>
                    <span class="text-amber-400 text-lg sm:text-xl animate-bounce-slow">🌀</span>
                    <span class="h-[2px] w-16 sm:w-24 bg-amber-300/70 animate-expand"></span>
                </div>
                <p class="mt-3 sm:mt-4 text-center text-xs sm:text-sm text-stone-700 leading-relaxed px-1">Biarkan <span class="inline-block rounded-md bg-pink-300/60 px-2 py-0.5 font-semibold text-stone-900 animate-pulse-slow">TrendyLook</span> menganalisis<br/>Bentuk Wajahmu Dan Temukan Gaya<br/>Rambut Terbaik Untuk Tampil Lebih Percaya Diri!</p>
                <div class="mt-4 sm:mt-5 flex justify-center">
                    <a href="{{ route('scan') }}" class="group inline-flex items-center gap-2 sm:gap-3 rounded-2xl bg-gradient-to-r from-pink-300/80 to-pink-400/80 px-5 sm:px-6 py-2.5 sm:py-3 text-xs sm:text-sm font-semibold text-stone-900 shadow-md hover:shadow-xl hover:from-pink-400 hover:to-pink-500 active:translate-y-px active:shadow transition-all duration-300 touch-manipulation relative overflow-hidden">
                        <!-- Shimmer effect -->
                        <span class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 bg-gradient-to-r from-transparent via-white/20 to-transparent"></span>
                        <!-- Icon kamera -->
                        <span class="grid h-7 w-7 sm:h-8 sm:w-8 place-items-center rounded-xl bg-pink-100/60 border border-stone-300 text-stone-700 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-12 relative z-10">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 sm:h-5 sm:w-5">
                                <path d="M4 7h3l2-2h6l2 2h3a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2Z" stroke-width="1.5"/>
                                <circle cx="12" cy="13" r="3.5" stroke-width="1.5"/>
                            </svg>
                        </span>
                        <span class="whitespace-nowrap relative z-10">Mulai Scan Sekarang</span>
                        <!-- Chevron kanan -->
                        <span class="grid h-5 w-5 sm:h-6 sm:w-6 place-items-center rounded-full bg-pink-100/60 border border-stone-300 text-stone-700 text-sm sm:text-base transition-transform duration-300 group-hover:translate-x-1 relative z-10">›</span>
                    </a>
                </div>
            </section>

            <!-- Visit Section -->
            <section class="mt-6 sm:mt-8 animate-fade-in-up">
                <h3 class="text-center text-lg sm:text-xl font-semibold px-2">Kunjungi Trendy Salon<br class="sm:hidden"/> Terdekat Anda Sekarang</h3>
                <div class="mt-4 sm:mt-5 flex justify-center">
                    <div class="relative group">
                        <div class="h-32 w-32 sm:h-44 sm:w-44 overflow-hidden rounded-full shadow-lg transition-transform duration-500 group-hover:scale-110 group-hover:shadow-2xl">
                            <img src="{{ asset('img/home2.jpg') }}" alt="Salon" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-125"/>
                        </div>
                        <!-- Badge di luar lingkaran -->
                        <div class="absolute -right-4 sm:-right-6 top-4 sm:top-6 animate-float">
                            <div class="h-14 w-14 sm:h-20 sm:w-20 rounded-full bg-gradient-to-br from-amber-200 to-amber-300 shadow-lg grid place-items-center text-center text-[8px] sm:text-[10px] font-bold text-stone-700 tracking-wide leading-tight px-1 transition-all duration-300 hover:scale-110 hover:shadow-xl hover:from-amber-300 hover:to-amber-400">
                                BUKA<br/>SETIAP HARI<br/>
                                <span class="font-bold">08:00-20:00 WIB</span>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="mx-auto mt-4 sm:mt-5 max-w-sm rounded-xl bg-stone-100 px-3 sm:px-4 py-2.5 sm:py-3 text-center text-[10px] sm:text-xs font-semibold text-stone-700 shadow tracking-wide transition-all duration-300 hover:shadow-lg hover:bg-stone-50">
                    *DISARANKAN <span class="font-bold text-pink-600">RESERVASI</span> TERLEBIH DAHULU
                </p>
            </section>

            <!-- Branches with dropdown to show website/map -->
            <section class="mt-5 sm:mt-6 space-y-3 sm:space-y-4">
                <!-- Giwangan -->
                <div class="rounded-xl bg-white shadow-md hover:shadow-xl transition-all duration-300 animate-fade-in-up group">
                    <button type="button" class="branch-toggle flex w-full items-start gap-2 sm:gap-3 px-3 sm:px-4 py-3 sm:py-4 touch-manipulation transition-colors duration-200 hover:bg-stone-50 rounded-t-xl" data-target="#branch-giwangan">
                        <span class="grid h-5 w-5 sm:h-6 sm:w-6 place-items-center rounded-full bg-gradient-to-br from-amber-200 to-amber-300 text-stone-800 transition-all duration-300 flex-shrink-0 mt-0.5 group-hover:scale-110 group-hover:from-amber-300 group-hover:to-amber-400">›</span>
                        <div class="text-left flex-1 min-w-0">
                            <p class="font-semibold text-sm sm:text-base group-hover:text-pink-600 transition-colors duration-200">Cabang Giwangan</p>
                            <p class="text-xs sm:text-sm text-stone-600 mt-0.5 leading-relaxed">Jl. Pramuka No39A, Prenggan, Kec. Kotagede, Kota Yogyakarta, DIY</p>
                        </div>
                    </button>
                    <div id="branch-giwangan" class="hidden border-t border-stone-200 animate-slide-down">
                        <div class="px-3 sm:px-4 py-2.5 sm:py-3 text-[10px] sm:text-xs text-stone-600">Website/Maps</div>
                        <div class="px-3 sm:px-4 pb-3 sm:pb-4">
                            <div class="aspect-video w-full overflow-hidden rounded-lg border shadow-inner">
                                <iframe class="h-full w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3952.676347770811!2d110.390335!3d-7.824039600000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a573c8729ad31%3A0xcb31d1555a656237!2zVHJlbmR5IFNhbG9uIOqni-qmoOqmv-qmvOqmpOqngOqmneqngOqmquqngOqmseqmreqmuuqmtOqmpOqngA!5e0!3m2!1sid!2sid!4v1763528154812!5m2!1sid!2sid"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sapen -->
                <div class="rounded-xl bg-white shadow-md hover:shadow-xl transition-all duration-300 animate-fade-in-up group">
                    <button type="button" class="branch-toggle flex w-full items-start gap-2 sm:gap-3 px-3 sm:px-4 py-3 sm:py-4 touch-manipulation transition-colors duration-200 hover:bg-stone-50 rounded-t-xl" data-target="#branch-sapen">
                        <span class="grid h-5 w-5 sm:h-6 sm:w-6 place-items-center rounded-full bg-gradient-to-br from-amber-200 to-amber-300 text-stone-800 transition-all duration-300 flex-shrink-0 mt-0.5 group-hover:scale-110 group-hover:from-amber-300 group-hover:to-amber-400">›</span>
                        <div class="text-left flex-1 min-w-0">
                            <p class="font-semibold text-sm sm:text-base group-hover:text-pink-600 transition-colors duration-200">Cabang Sapen</p>
                            <p class="text-xs sm:text-sm text-stone-600 mt-0.5 leading-relaxed">Jl. bimaskati No45 A, Demangan, Kec. Gondokusuman, Kota Yogyakarta, DIY</p>
                        </div>
                    </button>
                    <div id="branch-sapen" class="hidden border-t border-stone-200 animate-slide-down">
                        <div class="px-3 sm:px-4 py-2.5 sm:py-3 text-[10px] sm:text-xs text-stone-600">Website/Maps</div>
                        <div class="px-3 sm:px-4 pb-3 sm:pb-4">
                            <div class="aspect-video w-full overflow-hidden rounded-lg border shadow-inner">
                                <iframe class="h-full w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.0147614538614!2d110.391307!3d-7.788259000000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a59da72a704e5%3A0x5b7cb4bc6028e8ab!2zQmUmWW91IFNhbG9uLyBUcmVuZHkgU2Fsb24o6qeL6qan6qa6JuqmquqmuuqmtOqmiOqmseqmreqmuuqmtOqmpOqngC_qpqDqpr_qprzqpqTqp4Dqpp3qp4Dqpqrqp4DqprHqpq3qprrqprTqpqTqp4Ap!5e0!3m2!1sid!2sid!4v1763528188007!5m2!1sid!2sid"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kotabaru -->
                <div class="rounded-xl bg-white shadow-md hover:shadow-xl transition-all duration-300 animate-fade-in-up group">
                    <button type="button" class="branch-toggle flex w-full items-start gap-2 sm:gap-3 px-3 sm:px-4 py-3 sm:py-4 touch-manipulation transition-colors duration-200 hover:bg-stone-50 rounded-t-xl" data-target="#branch-kotabaru">
                        <span class="grid h-5 w-5 sm:h-6 sm:w-6 place-items-center rounded-full bg-gradient-to-br from-amber-200 to-amber-300 text-stone-800 transition-all duration-300 flex-shrink-0 mt-0.5 group-hover:scale-110 group-hover:from-amber-300 group-hover:to-amber-400">›</span>
                        <div class="text-left flex-1 min-w-0">
                            <p class="font-semibold text-sm sm:text-base group-hover:text-pink-600 transition-colors duration-200">Cabang Kotabaru</p>
                            <p class="text-xs sm:text-sm text-stone-600 mt-0.5 leading-relaxed">Jl. Kiasakti Timur No14, Kotabaru, Kec. Danurejan, Kota Yogyakarta, DIY</p>
                        </div>
                    </button>
                    <div id="branch-kotabaru" class="hidden border-t border-stone-200 animate-slide-down">
                        <div class="px-3 sm:px-4 py-2.5 sm:py-3 text-[10px] sm:text-xs text-stone-600">Website/Maps</div>
                        <div class="px-3 sm:px-4 pb-3 sm:pb-4">
                            <div class="aspect-video w-full overflow-hidden rounded-lg border shadow-inner">
                                <iframe class="h-full w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.0096117877865!2d110.3760499!3d-7.7888047!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a582cf3caa41d%3A0xe997d35e6c4041d5!2sTrendy%20Salon!5e0!3m2!1sid!2sid!4v1763528212373!5m2!1sid!2sid"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seturan -->
                <div class="rounded-xl bg-white shadow-md hover:shadow-xl transition-all duration-300 animate-fade-in-up group">
                    <button type="button" class="branch-toggle flex w-full items-start gap-2 sm:gap-3 px-3 sm:px-4 py-3 sm:py-4 touch-manipulation transition-colors duration-200 hover:bg-stone-50 rounded-t-xl" data-target="#branch-seturan">
                        <span class="grid h-5 w-5 sm:h-6 sm:w-6 place-items-center rounded-full bg-gradient-to-br from-amber-200 to-amber-300 text-stone-800 transition-all duration-300 flex-shrink-0 mt-0.5 group-hover:scale-110 group-hover:from-amber-300 group-hover:to-amber-400">›</span>
                        <div class="text-left flex-1 min-w-0">
                            <p class="font-semibold text-sm sm:text-base group-hover:text-pink-600 transition-colors duration-200">Cabang Seturan</p>
                            <p class="text-xs sm:text-sm text-stone-600 mt-0.5 leading-relaxed">Jl. Sekolan Mataram No430, Pringwulung, Condongcatur, Kec. Depok, Kabupaten Sleman, DIY</p>
                        </div>
                    </button>
                    <div id="branch-seturan" class="hidden border-t border-stone-200 animate-slide-down">
                        <div class="px-3 sm:px-4 py-2.5 sm:py-3 text-[10px] sm:text-xs text-stone-600">Website/Maps</div>
                        <div class="px-3 sm:px-4 pb-3 sm:pb-4">
                            <div class="aspect-video w-full overflow-hidden rounded-lg border shadow-inner">
                                <iframe class="h-full w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.204400391403!2d110.39664289999999!3d-7.7681368!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a591b65a0040f%3A0x97bccaa03a2012a6!2sTrendy%20Salon%204!5e0!3m2!1sid!2sid!4v1763528256200!5m2!1sid!2sid"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <footer class="mt-8 sm:mt-10 pb-8 sm:pb-10 text-center text-[10px] sm:text-xs text-stone-500">
                &copy; {{ date('Y') }} Trendy Salon
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