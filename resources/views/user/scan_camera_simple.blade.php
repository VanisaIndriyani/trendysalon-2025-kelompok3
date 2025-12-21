<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
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
        <!-- ✅ JS TIDAK DIPERLUKAN - PAKAI FORM UPLOAD (TANPA JS) -->
    </head>
    <body class="bg-stone-200 font-sans text-stone-800">
        <!-- Header -->
        <header class="sticky top-0 z-30 bg-pink-200/90 backdrop-blur border-b border-pink-300/40 transition-all duration-300 shadow-sm">
            <div class="mx-auto max-w-screen-md px-3 sm:px-4 py-2.5 sm:py-3 flex items-center gap-2 sm:gap-3">
                <a href="{{ route('scan') }}" class="grid h-8 w-8 sm:h-9 sm:w-9 place-items-center rounded-lg border border-stone-300 bg-pink-100/60 text-stone-700 touch-manipulation transition-all duration-300 hover:bg-pink-200 hover:scale-110 hover:shadow-md group" aria-label="Kembali">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 sm:h-5 sm:w-5 transition-transform duration-300 group-hover:-translate-x-1"><path d="M15 6l-6 6 6 6" stroke-width="1.5"/></svg>
                </a>
                <h1 class="text-base sm:text-lg font-semibold bg-gradient-to-r from-pink-600 to-amber-600 bg-clip-text text-transparent">Scan Wajah & Rambut</h1>
            </div>
        </header>

        <main class="mx-auto max-w-screen-md px-3 sm:px-4">
            <section class="mt-3 sm:mt-4">
                <div class="rounded-xl bg-gradient-to-r from-green-100 via-green-50 to-emerald-100 px-3 sm:px-4 py-3 sm:py-4 text-center shadow-md">
                    <p class="text-[11px] sm:text-xs text-stone-700 leading-relaxed font-medium">
                        <span class="font-semibold text-green-700">Upload foto wajah Anda untuk analisis AI</span><br/>
                        <span class="text-[10px] sm:text-xs">Pastikan wajah terlihat jelas dan hanya 1 orang</span>
                    </p>
                </div>

                <!-- ✅ FORM UPLOAD (TANPA JS) -->
                <div class="mt-3 sm:mt-4 rounded-xl bg-gradient-to-r from-pink-100 via-pink-50 to-amber-100 border-2 border-pink-200 px-4 py-4">
                    <p class="text-xs sm:text-sm font-semibold text-pink-800 mb-3 text-center">📸 Upload Foto Wajah untuk Analisis AI</p>
                    <form action="{{ route('scan.analyze') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div>
                            <label for="image_file" class="block text-xs text-pink-700 mb-1 font-medium">Pilih Foto Wajah:</label>
                            <input type="file" id="image_file" name="image" accept="image/*" capture="user" required class="w-full text-xs text-pink-800 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-pink-500 file:text-white hover:file:bg-pink-600">
                            <p class="mt-1 text-[10px] text-pink-600">Pastikan wajah terlihat jelas dan hanya 1 orang</p>
                        </div>
                        <div>
                            <label for="user_name_fallback" class="block text-xs text-pink-700 mb-1 font-medium">Nama (opsional):</label>
                            <input type="text" id="user_name_fallback" name="user_name" class="w-full px-3 py-2 text-xs rounded-lg border border-pink-300 focus:outline-none focus:ring-2 focus:ring-pink-500">
                        </div>
                        <div>
                            <label for="user_phone_fallback" class="block text-xs text-pink-700 mb-1 font-medium">No. HP (opsional):</label>
                            <input type="text" id="user_phone_fallback" name="user_phone" class="w-full px-3 py-2 text-xs rounded-lg border border-pink-300 focus:outline-none focus:ring-2 focus:ring-pink-500">
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-pink-500 to-pink-600 text-white font-semibold py-2.5 px-4 rounded-lg text-xs hover:from-pink-600 hover:to-pink-700 transition-all duration-300 shadow-md hover:shadow-lg">
                            🚀 Analisis dengan AI
                        </button>
                    </form>
                </div>
            </section>
        </main>
        
        <!-- ✅ TAMPILKAN PESAN ERROR DARI SESSION (JIKA ADA) -->
        @if(session('error'))
        <div class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 max-w-[90vw] px-4 py-3 bg-red-50 border-2 border-red-200 rounded-xl shadow-lg">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <p class="font-semibold text-sm text-red-900">Error</p>
                    <p class="text-xs text-red-700 mt-1">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif
        
        @if(session('success'))
        <div class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 max-w-[90vw] px-4 py-3 bg-green-50 border-2 border-green-200 rounded-xl shadow-lg">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <p class="font-semibold text-sm text-green-900">Berhasil</p>
                    <p class="text-xs text-green-700 mt-1">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif
    </body>
</html>

