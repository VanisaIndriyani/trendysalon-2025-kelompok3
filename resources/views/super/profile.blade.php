@extends('super.layout')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold">Profil</h1>
            <p class="mt-1 text-sm text-stone-600">Kelola foto profil dan ubah password akun super admin.</p>
        </div>
    </div>

    @php
        $uid = session('super_user_id');
        $u = \App\Models\User::find($uid);
        $displayName = $u?->name ?? 'Super Admin';
        // Gunakan AvatarHelper untuk generate avatar otomatis
        $avatarUrl = \App\Helpers\AvatarHelper::getAvatarUrl($uid, $displayName, 'super');
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
                <form method="POST" action="{{ route('super.profile.photo') }}" enctype="multipart/form-data" class="flex items-center gap-3">
                    @csrf
                    <input type="file" name="avatar" accept="image/*" class="text-sm" required />
                    <button type="submit" class="px-4 py-2 rounded-xl bg-pink-600 text-white hover:bg-pink-700">Upload</button>
                </form>
            </div>
        </div>

        <!-- Ubah Password -->
        <div class="rounded-2xl ring-1 ring-stone-200 bg-white p-6 space-y-4">
            <h2 class="text-lg font-bold">Ubah Password</h2>
            <form method="POST" action="{{ route('super.profile.password') }}" class="space-y-3">
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
        });
    </script>
</div>
@endsection