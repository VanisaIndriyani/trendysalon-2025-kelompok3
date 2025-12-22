@extends('admin.layout')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold">Profil</h1>
            <p class="mt-1 text-sm text-stone-600">Kelola foto profil dan ubah password akun admin.</p>
        </div>
    </div>

    @php
        $uid = session('admin_user_id');
        $u = \App\Models\User::find($uid);
        $displayName = $u?->name ?? 'Admin';
        // Gunakan AvatarHelper untuk generate avatar otomatis
        $avatarUrl = \App\Helpers\AvatarHelper::getAvatarUrl($uid, $displayName, 'admin');
    @endphp

    @if(session('success'))
        <div id="center-notif" class="fixed inset-0 z-50 grid place-items-center">
            <div class="absolute inset-0 bg-black/30"></div>
            <div class="relative z-10 bg-white rounded-2xl ring-1 ring-pink-200 p-6 shadow-xl flex items-center gap-3">
                <div class="h-8 w-8 rounded-full grid place-items-center bg-pink-50 ring-1 ring-pink-200 text-pink-600">✓</div>
                <div>
                    <p class="font-semibold text-stone-900">Berhasil</p>
                    <p class="text-sm text-stone-600">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div id="center-error" class="fixed inset-0 z-50 grid place-items-center">
            <div class="absolute inset-0 bg-black/30"></div>
            <div class="relative z-10 bg-white rounded-2xl ring-1 ring-red-200 p-6 shadow-xl flex items-center gap-3">
                <div class="h-8 w-8 rounded-full grid place-items-center bg-red-50 ring-1 ring-red-200 text-red-600">!</div>
                <div>
                    <p class="font-semibold text-stone-900">Gagal</p>
                    <p class="text-sm text-stone-600">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Foto Profil -->
        <div class="rounded-2xl ring-1 ring-stone-200 bg-white p-6 space-y-4">
            <h2 class="text-lg font-bold">Foto Profil</h2>
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 rounded-full overflow-hidden ring-2 ring-pink-200">
                    <img src="{{ $avatarUrl }}" alt="Avatar" class="h-full w-full object-cover" />
                </div>
                <form method="POST" action="{{ route('admin.profile.photo') }}" enctype="multipart/form-data" class="flex items-center gap-3">
                    @csrf
                    <input type="file" name="avatar" accept="image/*" class="text-sm" required />
                    <button type="submit" class="px-4 py-2 rounded-xl bg-pink-600 text-white hover:bg-pink-700">Upload</button>
                </form>
            </div>
        </div>

        <!-- Ubah Password -->
        <div class="rounded-2xl ring-1 ring-stone-200 bg-white p-6 space-y-4">
            <h2 class="text-lg font-bold">Ubah Password</h2>
            <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="text-sm font-medium">Password Baru</label>
                    <div class="mt-1 flex items-center gap-2">
                        <input id="pwd" name="password" type="password" class="w-full rounded-xl ring-1 ring-stone-200 px-3 py-2" minlength="8" required />
                        <button type="button" id="toggle-pwd" class="h-9 w-9 rounded-xl ring-1 ring-stone-200 bg-stone-50">👁</button>
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium">Konfirmasi Password</label>
                    <div class="mt-1 flex items-center gap-2">
                        <input id="pwdc" name="password_confirmation" type="password" class="w-full rounded-xl ring-1 ring-stone-200 px-3 py-2" minlength="8" required />
                        <button type="button" id="toggle-pwdc" class="h-9 w-9 rounded-xl ring-1 ring-stone-200 bg-stone-50">👁</button>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-pink-600 text-white hover:bg-pink-700">Update Password</button>
                </div>
            </form>
        </div>
    </div>

    <!-- QR Code untuk Scan User -->
    <div class="rounded-2xl ring-1 ring-stone-200 bg-white p-6 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold">QR Code untuk Scan User</h2>
                <p class="text-sm text-stone-600 mt-1">Scan QR code ini untuk mengarahkan user ke halaman home</p>
            </div>
        </div>
        <div class="flex flex-col items-center gap-4">
            <div class="bg-white p-4 rounded-xl ring-1 ring-stone-200 inline-block">
                <div id="qrcode-container" class="w-64 h-64 flex items-center justify-center">
                    <div class="text-stone-400 text-sm">Loading QR Code...</div>
                </div>
            </div>
            <div class="flex gap-3">
                <button onclick="printQRCode()" class="px-6 py-2 rounded-xl bg-pink-600 text-white hover:bg-pink-700 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak QR Code
                </button>
                <button onclick="downloadQRCode()" class="px-6 py-2 rounded-xl bg-stone-100 text-stone-700 hover:bg-stone-200 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const c = id => document.getElementById(id);
            const toggle = (inputId, btnId) => {
                const i = c(inputId), b = c(btnId);
                b?.addEventListener('click', () => {
                    if (!i) return;
                    i.type = i.type === 'password' ? 'text' : 'password';
                });
            };
            toggle('pwd','toggle-pwd');
            toggle('pwdc','toggle-pwdc');
            const notif = document.getElementById('center-notif');
            if (notif) setTimeout(()=>notif.classList.add('hidden'), 5000);
            const err = document.getElementById('center-error');
            if (err) setTimeout(()=>err.classList.add('hidden'), 5000);

            // Generate QR Code menggunakan API online
            const homeUrl = '{{ route("user.home") }}';
            const qrContainer = document.getElementById('qrcode-container');
            if (qrContainer) {
                const qrImg = document.createElement('img');
                const encodedUrl = encodeURIComponent(homeUrl);
                qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=256x256&data=${encodedUrl}`;
                qrImg.alt = 'QR Code';
                qrImg.className = 'w-64 h-64';
                qrImg.onload = function() {
                    qrContainer.innerHTML = '';
                    qrContainer.appendChild(qrImg);
                    window.qrImg = qrImg;
                };
                qrImg.onerror = function() {
                    qrContainer.innerHTML = '<div class="text-red-500 text-sm">Error loading QR Code. Silakan refresh halaman.</div>';
                };
            }
        });

        function printQRCode() {
            const homeUrl = '{{ route("user.home") }}';
            const encodedUrl = encodeURIComponent(homeUrl);
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=${encodedUrl}`;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Cetak QR Code - Trendy Salon</title>
                        <style>
                            body {
                                margin: 0;
                                padding: 40px;
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                justify-content: center;
                                min-height: 100vh;
                                font-family: Arial, sans-serif;
                            }
                            .qr-container {
                                text-align: center;
                            }
                            .qr-container img {
                                max-width: 400px;
                                height: auto;
                                border: 2px solid #000;
                                padding: 20px;
                                background: white;
                            }
                            .qr-title {
                                font-size: 24px;
                                font-weight: bold;
                                margin-bottom: 20px;
                            }
                            .qr-subtitle {
                                font-size: 14px;
                                color: #666;
                                margin-top: 20px;
                            }
                            @media print {
                                body {
                                    padding: 0;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="qr-container">
                            <div class="qr-title">QR Code Trendy Salon</div>
                            <img src="${qrUrl}" alt="QR Code" onload="window.focus(); window.print();" />
                            <div class="qr-subtitle">Scan untuk mengakses halaman home</div>
                        </div>
                    </body>
                </html>
            `);
            printWindow.document.close();
        }

        function downloadQRCode() {
            const homeUrl = '{{ route("user.home") }}';
            const encodedUrl = encodeURIComponent(homeUrl);
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=${encodedUrl}`;
            const link = document.createElement('a');
            link.download = 'qrcode-scan-user.png';
            link.href = qrUrl;
            link.click();
        }
    </script>
</div>
@endsection