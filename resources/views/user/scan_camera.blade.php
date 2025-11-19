<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
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
            };
        </script>
        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body class="bg-stone-200 font-sans text-stone-800" id="scanCameraPage">
        <!-- Header -->
        <header class="sticky top-0 z-30 bg-pink-200/90 backdrop-blur border-b border-pink-300/40">
            <div class="mx-auto max-w-screen-md px-4 py-3 flex items-center gap-3">
                <a href="{{ route('scan') }}" class="grid h-9 w-9 place-items-center rounded-lg border border-stone-300 bg-pink-100/60 text-stone-700" aria-label="Kembali">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5"><path d="M15 6l-6 6 6 6" stroke-width="1.5"/></svg>
                </a>
                <h1 class="text-lg font-semibold">Scan Wajah & Rambut</h1>
            </div>
        </header>

        <main class="mx-auto max-w-screen-md px-4">
            <section class="mt-4">
                <div class="rounded-xl bg-gradient-to-r from-pink-100 to-amber-100 px-4 py-4 text-center">
                    <p class="text-xs text-stone-700">Posisikan wajah dan rambut Anda di tengah frame<br/>untuk hasil terbaik</p>
                </div>

                <div class="mt-4 rounded-xl bg-stone-300 p-4 shadow">
                    <div class="rounded-xl border-2 border-sky-500 bg-stone-200 grid place-items-center h-[420px]">
                        <video id="cameraVideo" autoplay playsinline class="h-full w-full object-cover rounded-lg hidden"></video>
                        <div id="cameraPlaceholder" class="grid place-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-14 w-14 text-stone-700"><path d="M4 7h3l2-2h6l2 2h3a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2Z" stroke-width="1.5"/><circle cx="12" cy="13" r="3.5" stroke-width="1.5"/></svg>
                        </div>
                        <canvas id="cameraCanvas" class="hidden"></canvas>
                    </div>
                </div>

                <div class="mt-6 flex justify-center">
                    <button id="captureBtn" type="button" aria-label="Ambil foto" class="group grid h-24 w-24 place-items-center rounded-full bg-pink-300/70 ring-4 ring-pink-200 shadow-lg transition transform active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-12 w-12 text-stone-700">
                            <path d="M4 7h3l2-2h6l2 2h3a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2Z" stroke-width="1.5"/>
                            <circle cx="12" cy="13" r="3.5" stroke-width="1.5"/>
                        </svg>
                    </button>
                </div>
            </section>
        </main>
    </body>
</html>