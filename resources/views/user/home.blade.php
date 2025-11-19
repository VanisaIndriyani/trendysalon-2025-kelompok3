<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
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
        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body class="bg-stone-200 font-sans text-stone-800">
        <!-- Header -->
        <header class="sticky top-0 z-30 bg-pink-200/90 backdrop-blur">
            <div class="mx-auto max-w-screen-md px-4 py-3 flex items-center gap-3">
                <a href="{{ route('admin.login') }}" class="inline-flex items-center" title="Login Admin">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo Trendy Salon" class="h-10 w-auto rounded-sm shadow hover:opacity-90" />
                </a>
                <div class="leading-tight">
                    <p class="text-xs tracking-widest text-stone-600">Trendy Salon</p>
                    <p class="text-sm font-medium">Layanan Terbaik Kami</p>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-screen-md px-4">
            <!-- Hero Slider -->
            <section class="mt-4">
                <div id="heroSlider" class="relative overflow-hidden rounded-xl shadow-lg">
                    <!-- Slides -->
                    <div class="slide">
                        <img src="{{ asset('img/home.jpg') }}" alt="Perawatan Rambut" class="w-full h-64 sm:h-80 object-cover" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                        <div class="absolute bottom-6 left-6 right-6 text-white">
                            <p class="text-2xl sm:text-3xl font-extrabold">Rasakan Perawatan Premium</p>
                            <p class="text-xl sm:text-2xl font-semibold mt-1">Dengan Sentuhan Profesional</p>
                            <p class="mt-2 text-xs sm:text-sm tracking-widest">DI TRENDY SALON</p>
                        </div>
                        <div class="absolute top-4 right-4">
                            <div class="h-16 w-16 sm:h-20 sm:w-20 rounded-full bg-white/70 grid place-items-center text-[10px] sm:text-xs font-semibold text-stone-700">
                                STAY<br/>SAFE &<br/>HEALTHY
                            </div>
                        </div>
                    </div>
                    <!-- Single image, no controls -->
                </div>
            </section>

            <!-- Intro & CTA -->
            <section class="mt-6 rounded-xl bg-stone-100 px-4 py-6 shadow">
                <h2 class="text-center text-xl sm:text-2xl font-extrabold uppercase tracking-wide">TEMUKAN MODEL POTONGAN<br class="sm:hidden"/> RAMBUT TERBAIK ANDA!</h2>
                <div class="mt-2 flex items-center justify-center gap-4">
                    <span class="h-[2px] w-24 bg-amber-300/70"></span>
                    <span class="text-amber-400">🌀</span>
                    <span class="h-[2px] w-24 bg-amber-300/70"></span>
                </div>
                <p class="mt-4 text-center text-sm text-stone-700 leading-relaxed">Biarkan <span class="inline-block rounded-md bg-pink-300/60 px-2 py-0.5 font-semibold text-stone-900">TrendyLook</span> menganalisis<br/>Bentuk Wajahmu Dan Temukan Gaya<br/>Rambut Terbaik Untuk Tampil Lebih Percaya Diri!</p>
                <div class="mt-4 flex justify-center">
                    <a href="{{ route('scan') }}" class="inline-flex items-center gap-3 rounded-2xl bg-pink-300/80 px-6 py-3 text-sm font-semibold text-stone-900 shadow-md hover:bg-pink-400 active:translate-y-px active:shadow">
                        <!-- Icon kamera -->
                        <span class="grid h-8 w-8 place-items-center rounded-xl bg-pink-100/60 border border-stone-300 text-stone-700">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5">
                                <path d="M4 7h3l2-2h6l2 2h3a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2Z" stroke-width="1.5"/>
                                <circle cx="12" cy="13" r="3.5" stroke-width="1.5"/>
                            </svg>
                        </span>
                        Mulai Scan Sekarang
                        <!-- Chevron kanan -->
                        <span class="grid h-6 w-6 place-items-center rounded-full bg-pink-100/60 border border-stone-300 text-stone-700">›</span>
                    </a>
                </div>
            </section>

            <!-- Visit Section -->
            <section class="mt-8">
                <h3 class="text-center text-xl font-semibold">Kunjungi Trendy Salon<br class="sm:hidden"/> Terdekat Anda Sekarang</h3>
                <div class="mt-5 flex justify-center">
                    <div class="relative">
                        <div class="h-44 w-44 overflow-hidden rounded-full shadow-lg">
                            <img src="{{ asset('img/home2.jpg') }}" alt="Salon" class="h-full w-full object-cover"/>
                        </div>
                        <!-- Badge di luar lingkaran -->
                        <div class="absolute -right-6 top-6">
                            <div class="h-20 w-20 rounded-full bg-amber-200 shadow grid place-items-center text-center text-[10px] font-bold text-stone-700 tracking-wide">
                                BUKA<br/>SETIAP HARI<br/>
                                <span class="font-bold">08:00-20:00 WIB</span>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="mx-auto mt-5 max-w-sm rounded-xl bg-stone-100 px-4 py-3 text-center text-xs font-semibold text-stone-700 shadow tracking-wide">
                    *DISARANKAN <span class="font-bold">RESERVASI</span> TERLEBIH DAHULU
                </p>
            </section>

            <!-- Branches with dropdown to show website/map -->
            <section class="mt-6 space-y-4">
                <!-- Giwangan -->
                <div class="rounded-xl bg-white shadow">
                    <button type="button" class="branch-toggle flex w-full items-start gap-3 px-4 py-4" data-target="#branch-giwangan">
                        <span class="grid h-6 w-6 place-items-center rounded-full bg-amber-200 text-stone-800 transition-transform">›</span>
                        <div class="text-left">
                            <p class="font-semibold">Cabang Giwangan</p>
                            <p class="text-sm text-stone-600">Jl. Pramuka No39A, Prenggan, Kec. Kotagede, Kota Yogyakarta, DIY</p>
                        </div>
                    </button>
                    <div id="branch-giwangan" class="hidden border-t border-stone-200">
                        <div class="px-4 py-3 text-xs text-stone-600">Website/Maps</div>
                        <div class="px-4 pb-4">
                            <div class="aspect-video w-full overflow-hidden rounded-lg border">
                                <iframe class="h-full w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3952.676347770811!2d110.390335!3d-7.824039600000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a573c8729ad31%3A0xcb31d1555a656237!2zVHJlbmR5IFNhbG9uIOqni-qmoOqmv-qmvOqmpOqngOqmneqngOqmquqngOqmseqmreqmuuqmtOqmpOqngA!5e0!3m2!1sid!2sid!4v1763528154812!5m2!1sid!2sid"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sapen -->
                <div class="rounded-xl bg-white shadow">
                    <button type="button" class="branch-toggle flex w-full items-start gap-3 px-4 py-4" data-target="#branch-sapen">
                        <span class="grid h-6 w-6 place-items-center rounded-full bg-amber-200 text-stone-800 transition-transform">›</span>
                        <div class="text-left">
                            <p class="font-semibold">Cabang Sapen</p>
                            <p class="text-sm text-stone-600">Jl. bimaskati No45 A, Demangan, Kec. Gondokusuman, Kota Yogyakarta, DIY</p>
                        </div>
                    </button>
                    <div id="branch-sapen" class="hidden border-t border-stone-200">
                        <div class="px-4 py-3 text-xs text-stone-600">Website/Maps</div>
                        <div class="px-4 pb-4">
                            <div class="aspect-video w-full overflow-hidden rounded-lg border">
                                <iframe class="h-full w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.0147614538614!2d110.391307!3d-7.788259000000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a59da72a704e5%3A0x5b7cb4bc6028e8ab!2zQmUmWW91IFNhbG9uLyBUcmVuZHkgU2Fsb24o6qeL6qan6qa6JuqmquqmuuqmtOqmiOqmseqmreqmuuqmtOqmpOqngC_qpqDqpr_qprzqpqTqp4Dqpp3qp4Dqpqrqp4DqprHqpq3qprrqprTqpqTqp4Ap!5e0!3m2!1sid!2sid!4v1763528188007!5m2!1sid!2sid"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kotabaru -->
                <div class="rounded-xl bg-white shadow">
                    <button type="button" class="branch-toggle flex w-full items-start gap-3 px-4 py-4" data-target="#branch-kotabaru">
                        <span class="grid h-6 w-6 place-items-center rounded-full bg-amber-200 text-stone-800 transition-transform">›</span>
                        <div class="text-left">
                            <p class="font-semibold">Cabang Kotabaru</p>
                            <p class="text-sm text-stone-600">Jl. Kiasakti Timur No14, Kotabaru, Kec. Danurejan, Kota Yogyakarta, DIY</p>
                        </div>
                    </button>
                    <div id="branch-kotabaru" class="hidden border-t border-stone-200">
                        <div class="px-4 py-3 text-xs text-stone-600">Website/Maps</div>
                        <div class="px-4 pb-4">
                            <div class="aspect-video w-full overflow-hidden rounded-lg border">
                                <iframe class="h-full w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.0096117877865!2d110.3760499!3d-7.7888047!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a582cf3caa41d%3A0xe997d35e6c4041d5!2sTrendy%20Salon!5e0!3m2!1sid!2sid!4v1763528212373!5m2!1sid!2sid"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seturan -->
                <div class="rounded-xl bg-white shadow">
                    <button type="button" class="branch-toggle flex w-full items-start gap-3 px-4 py-4" data-target="#branch-seturan">
                        <span class="grid h-6 w-6 place-items-center rounded-full bg-amber-200 text-stone-800 transition-transform">›</span>
                        <div class="text-left">
                            <p class="font-semibold">Cabang Seturan</p>
                            <p class="text-sm text-stone-600">Jl. Sekolan Mataram No430, Pringwulung, Condongcatur, Kec. Depok, Kabupaten Sleman, DIY</p>
                        </div>
                    </button>
                    <div id="branch-seturan" class="hidden border-t border-stone-200">
                        <div class="px-4 py-3 text-xs text-stone-600">Website/Maps</div>
                        <div class="px-4 pb-4">
                            <div class="aspect-video w-full overflow-hidden rounded-lg border">
                                <iframe class="h-full w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.204400391403!2d110.39664289999999!3d-7.7681368!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a591b65a0040f%3A0x97bccaa03a2012a6!2sTrendy%20Salon%204!5e0!3m2!1sid!2sid!4v1763528256200!5m2!1sid!2sid"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <footer class="mt-10 pb-10 text-center text-xs text-stone-500">
                &copy; {{ date('Y') }} Trendy Salon
            </footer>
        </main>

        <!-- Small helper styles for slider -->
        <style>
            #heroSlider .slide { position: relative; }
        </style>
        <script>
            // Simple dropdown toggles for branches
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.branch-toggle').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const targetSel = btn.getAttribute('data-target');
                        const panel = targetSel ? document.querySelector(targetSel) : null;
                        if (!panel) return;
                        panel.classList.toggle('hidden');
                        // rotate chevron
                        const chevron = btn.querySelector('span');
                        if (chevron) chevron.style.transform = panel.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(90deg)';
                    });
                });
            });
        </script>
    </body>
</html>