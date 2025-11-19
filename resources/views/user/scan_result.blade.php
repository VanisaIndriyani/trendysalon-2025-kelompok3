<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
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
        </script>
        <script src="{{ asset('js/app.js') }}" defer></script>
        <!-- MediaPipe FaceMesh (CDN) for automatic landmark detection -->
        <script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/face_mesh.js"></script>
    </head>
    <body class="bg-stone-200 font-sans text-stone-800" id="scanResultPage">
        <!-- Header -->
        <header class="sticky top-0 z-30 bg-pink-200/90 backdrop-blur border-b border-pink-300/40">
            <div class="mx-auto max-w-screen-md px-4 py-3 flex items-center gap-3">
                <a href="{{ route('scan.camera') }}" class="grid h-9 w-9 place-items-center rounded-lg border border-stone-300 bg-pink-100/60 text-stone-700" aria-label="Kembali">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5"><path d="M15 6l-6 6 6 6" stroke-width="1.5"/></svg>
                </a>
                <h1 class="text-lg font-semibold">Hasil Analisis</h1>
            </div>
        </header>

        <main class="mx-auto max-w-screen-md px-4">
            <section class="mt-4">
                <div class="rounded-xl bg-gradient-to-r from-pink-100 to-amber-100 px-4 py-4 text-center">
                    <p class="font-semibold">Hasil Analisis Dipersonalisasi</p>
                    <p class="mt-1 text-xs text-stone-700">Rekomendasi gaya rambut terbaik berdasarkan foto, bentuk wajah, dan preferensi Anda.</p>
                </div>

                <!-- Preview hasil capture -->
                <div id="capturePreview" class="mt-4 hidden">
                    <img id="captureImage" alt="Hasil capture" class="mx-auto h-44 w-44 rounded-xl object-cover shadow" />
                </div>

                <!-- Try-on overlay area -->
                <div id="tryOnControls" class="mt-4 hidden">
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-stone-700">Coba model di wajah Anda</p>
                        <button id="tryOnClose" type="button" class="rounded-lg border border-stone-300 bg-white px-2 py-1 text-xs">Tutup</button>
                    </div>
                    <div id="tryOnStage" class="relative mx-auto mt-2 h-72 w-72 rounded-xl bg-stone-100 overflow-hidden shadow">
                        <img id="tryOnBase" class="absolute inset-0 h-full w-full object-cover" />
                        <img id="tryOnOverlay" class="absolute left-1/2 top-0 h-full object-contain pointer-events-none" />
                    </div>
                    <div class="mt-2 grid grid-cols-3 gap-3">
                        <label class="text-xs flex items-center gap-2">Skala
                            <input id="tryOnScale" type="range" min="80" max="140" value="100" class="w-full">
                        </label>
                        <label class="text-xs flex items-center gap-2">Posisi X
                            <input id="tryOnOffsetX" type="range" min="-60" max="60" value="0" class="w-full">
                        </label>
                        <label class="text-xs flex items-center gap-2">Posisi Y
                            <input id="tryOnOffsetY" type="range" min="-40" max="40" value="0" class="w-full">
                        </label>
                    </div>
                </div>

                <h3 class="mt-6 text-sm font-semibold">Rekomendasi untuk Anda</h3>
                <div id="loadingAnalysis" class="mt-2 text-xs text-stone-600">Sedang menganalisis foto Anda…</div>
                <div id="recommendations" class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-4"></div>
                <p class="mt-2 text-[11px] text-stone-600">Klik kartu rekomendasi untuk mencoba di wajah Anda.</p>
            </section>
        </main>
    </body>
</html>