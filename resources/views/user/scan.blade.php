<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
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
        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body class="bg-stone-200 font-sans text-stone-800">
        <!-- Header / Navigation -->
        <header class="sticky top-0 z-30 bg-pink-200/90 backdrop-blur border-b border-pink-300/40">
            <div class="mx-auto max-w-screen-md px-4 py-3 flex items-center gap-3">
                <a href="{{ route('user.home') }}" class="flex items-center gap-3">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo Trendy Salon" class="h-10 w-auto rounded-sm shadow" />
                    <div class="leading-tight">
                        <p class="text-xs tracking-widest text-stone-600">Trendy Salon</p>
                        <p class="text-sm font-medium">TrendyLook</p>
                    </div>
                </a>

                <!-- Desktop Nav -->
             

            </div>
          
        </header>

        <main class="mx-auto max-w-screen-md px-4">
            <!-- Scan Form -->
            <section class="mt-6 rounded-xl bg-stone-100 px-4 py-6 shadow">
                <div class="flex items-center gap-3">
                    <a href="{{ route('user.home') }}" class="grid h-9 w-9 place-items-center rounded-lg border border-stone-300 bg-pink-100/60 text-stone-700" aria-label="Kembali">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5"><path d="M15 6l-6 6 6 6" stroke-width="1.5"/></svg>
                    </a>
                    <h2 class="text-lg font-semibold">Informasi Awal</h2>
                </div>
                <div class="mt-4 rounded-xl bg-gradient-to-r from-pink-100 to-amber-100 px-4 py-4">
                    <p class="text-center font-semibold">Ceritakan Tentang Rambut Anda</p>
                    <p class="mt-1 text-center text-xs text-stone-700">jawab beberapa pertanyaan untuk hasil rekomendasi yang lebih akurat</p>
                </div>

                <form id="scanForm" class="mt-4 space-y-4" novalidate>
                    <div class="rounded-xl border border-stone-200 bg-white p-4 shadow-sm">
                        <label class="text-sm font-semibold">Nama <span class="text-pink-600">*</span></label>
                        <input id="nameInput" name="name" type="text" placeholder="Nama" class="mt-2 w-full rounded-lg border border-stone-300 bg-stone-50 px-3 py-2 outline-none focus:border-stone-500" required />
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white p-4 shadow-sm">
                        <label class="text-sm font-semibold">Nomor Ponsel <span class="text-pink-600">*</span></label>
                        <input id="phoneInput" name="phone" type="tel" placeholder="Nomor Ponsel" class="mt-2 w-full rounded-lg border border-stone-300 bg-stone-50 px-3 py-2 outline-none focus:border-stone-500" required />
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white p-4 shadow-sm">
                        <label class="text-sm font-semibold">Model Panjang Rambut <span class="text-pink-600">*</span></label>
                        <select id="lengthSelect" name="length" class="mt-2 w-full rounded-lg border border-stone-300 bg-stone-50 px-3 py-2 outline-none focus:border-stone-500" required>
                            <option value="" selected>Pilih Model Panjang Rambut</option>
                            @foreach(($lengths ?? []) as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white p-4 shadow-sm">
                        <label class="text-sm font-semibold">Jenis Rambut <span class="text-pink-600">*</span></label>
                        <select id="typeSelect" name="type" class="mt-2 w-full rounded-lg border border-stone-300 bg-stone-50 px-3 py-2 outline-none focus:border-stone-500" required>
                            <option value="" selected>Pilih Jenis Rambut</option>
                            @foreach(($types ?? []) as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white p-4 shadow-sm">
                        <label class="text-sm font-semibold">Tipe Rambut <span class="text-pink-600">*</span></label>
                        <select id="conditionSelect" name="condition" class="mt-2 w-full rounded-lg border border-stone-300 bg-stone-50 px-3 py-2 outline-none focus:border-stone-500" required>
                            <option value="" selected>Pilih Tipe Rambut</option>
                            @foreach(($conditions ?? []) as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-6 flex justify-center">
                        <button id="scanSubmit" data-target="{{ route('scan.camera') }}" type="button" class="inline-flex items-center gap-2 rounded-2xl bg-amber-200 px-6 py-3 text-sm font-semibold text-stone-900 shadow hover:bg-amber-300">
                            Lanjutkan Scan
                            <span class="grid h-6 w-6 place-items-center rounded-full bg-amber-100 border border-stone-300 text-stone-700">›</span>
                        </button>
                    </div>
                </form>
            </section>

            <footer class="mt-10 pb-10 text-center text-xs text-stone-500">
                &copy; {{ date('Y') }} Trendy Salon
            </footer>
        </main>
    </body>
</html>