<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Super Admin - Trendy Salon</title>
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
        <div class="h-screen overflow-hidden grid grid-cols-1 lg:grid-cols-[280px_1fr]">
            <!-- Sidebar -->
            <aside class="hidden lg:block bg-white/95 backdrop-blur rounded-r-2xl shadow-lg ring-1 ring-stone-200 p-5 h-full overflow-y-auto">
                <div class="flex justify-center">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo Trendy Salon" class="block h-16 sm:h-20 w-auto mx-auto rounded-sm shadow" />
                </div>
                <div class="mt-5 border-t border-stone-200"></div>

                <!-- Profile card -->
                @php
                    $uid = session('super_user_id');
                    $u = \App\Models\User::find($uid);
                    $displayName = $u?->name ?? 'Super Admin';
                    $displayEmail = $u?->email ?? 'super@trendysalon.com';
                    // Gunakan AvatarHelper untuk generate avatar otomatis
                    $avatarUrl = \App\Helpers\AvatarHelper::getAvatarUrl($uid, $displayName, 'super');
                @endphp
                <div class="mt-5 rounded-xl border border-pink-200 bg-pink-50 p-4 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full overflow-hidden ring-2 ring-pink-200">
                        <img src="{{ $avatarUrl }}" alt="Avatar" class="h-full w-full object-cover" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold">{{ $displayName }}</p>
                        <p class="text-xs text-stone-600">{{ $displayEmail }}</p>
                    </div>
                </div>

                <!-- Nav -->
                <nav class="mt-6 space-y-2">
                    <!-- Profil -->
                  
                    <!-- Dashboard -->
                    <a href="{{ route('super.dashboard') }}" class="relative flex items-center gap-3 rounded-xl px-4 py-3 border transition-all duration-150 {{ Route::is('super.dashboard') ? 'bg-pink-100 text-stone-900 border-pink-200 ring-1 ring-pink-200' : 'border-stone-200 hover:bg-pink-50' }}">
                        @if(Route::is('super.dashboard'))
                            <span class="absolute left-1 top-1/2 -translate-y-1/2 h-2 w-2 rounded-full bg-pink-400"></span>
                        @endif
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 text-pink-600">
                            <path d="M3 11l9-7 9 7" stroke-width="1.5"/>
                            <path d="M5 11v9h14v-9" stroke-width="1.5"/>
                            <path d="M10 20v-6h4v6" stroke-width="1.5"/>
                        </svg>
                        <span class="font-semibold">Dashboard</span>
                    </a>
                    <!-- Data Model Rambut -->
                    <a href="{{ route('super.models') }}" class="relative flex items-center gap-3 rounded-xl px-4 py-3 border transition-all duration-150 {{ Route::is('super.models') ? 'bg-pink-100 text-stone-900 border-pink-200 ring-1 ring-pink-200' : 'border-stone-200 hover:bg-pink-50' }}">
                        @if(Route::is('admin.models'))
                            <span class="absolute left-1 top-1/2 -translate-y-1/2 h-2 w-2 rounded-full bg-pink-400"></span>
                        @endif
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 text-pink-600">
                            <circle cx="6" cy="7" r="2.5" stroke-width="1.5"/>
                            <circle cx="6" cy="17" r="2.5" stroke-width="1.5"/>
                            <path d="M8 8l12 8" stroke-width="1.5"/>
                            <path d="M8 16l12-8" stroke-width="1.5"/>
                        </svg>
                        <span class="font-semibold">Data Model Rambut</span>
                    </a>
                    <!-- Data Vitamin Rambut -->
                    <a href="{{ route('super.vitamins') }}" class="relative flex items-center gap-3 rounded-xl px-4 py-3 border transition-all duration-150 {{ Route::is('super.vitamins') ? 'bg-pink-100 text-stone-900 border-pink-200 ring-1 ring-pink-200' : 'border-stone-200 hover:bg-pink-50' }}">
                        @if(Route::is('admin.vitamins'))
                            <span class="absolute left-1 top-1/2 -translate-y-1/2 h-2 w-2 rounded-full bg-pink-400"></span>
                        @endif
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 text-pink-600">
                            <rect x="4" y="6" width="16" height="12" rx="6" ry="6" stroke-width="1.5"/>
                            <path d="M12 7v10" stroke-width="1.5"/>
                        </svg>
                        <span class="font-semibold">Data Vitamin Rambut</span>
                    </a>
                    <!-- Analitik & Laporan -->
                    <a href="{{ route('super.reports') }}" class="relative flex items-center gap-3 rounded-xl px-4 py-3 border transition-all duration-150 {{ Route::is('super.reports') ? 'bg-pink-100 text-stone-900 border-pink-200 ring-1 ring-pink-200' : 'border-stone-200 hover:bg-pink-50' }}">
                        @if(Route::is('admin.reports'))
                            <span class="absolute left-1 top-1/2 -translate-y-1/2 h-2 w-2 rounded-full bg-pink-400"></span>
                        @endif
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 text-pink-600">
                            <path d="M4 20h16" stroke-width="1.5"/>
                            <rect x="6" y="11" width="3" height="7" rx="1" stroke-width="1.5"/>
                            <rect x="11" y="8" width="3" height="10" rx="1" stroke-width="1.5"/>
                            <rect x="16" y="13" width="3" height="5" rx="1" stroke-width="1.5"/>
                        </svg>
                        <span class="font-semibold">Analitik & Laporan</span>
                    </a>
                    <!-- Manajemen Admin (khusus Super Admin) -->
                    <a href="{{ route('super.admins') }}" class="relative flex items-center gap-3 rounded-xl px-4 py-3 border transition-all duration-150 {{ Route::is('super.admins') ? 'bg-pink-100 text-stone-900 border-pink-200 ring-1 ring-pink-200' : 'border-stone-200 hover:bg-pink-50' }}">
                        @if(Route::is('super.admins'))
                            <span class="absolute left-1 top-1/2 -translate-y-1/2 h-2 w-2 rounded-full bg-pink-400"></span>
                        @endif
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 text-pink-600">
                            <path d="M16 11c1.657 0 3-1.567 3-3.5S17.657 4 16 4s-3 1.567-3 3.5 1.343 3.5 3 3.5Z" stroke-width="1.5"/>
                            <path d="M8 11c1.657 0 3-1.567 3-3.5S9.657 4 8 4 5 5.567 5 7.5 6.343 11 8 11Z" stroke-width="1.5"/>
                            <path d="M4 20v-1c0-2.209 2.239-4 5-4s5 1.791 5 4v1" stroke-width="1.5"/>
                            <path d="M14 20v-1c0-1.652.928-3.1 2.333-3.843" stroke-width="1.5"/>
                        </svg>
                        <span class="font-semibold">Manajemen Admin</span>
                    </a>
                    <!-- Logout -->
                    <a href="{{ route('super.logout') }}" class="relative flex items-center gap-3 rounded-xl px-4 py-3 border border-stone-200 transition-all duration-150 hover:bg-pink-50">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 text-pink-600">
                            <path d="M10 5H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h4" stroke-width="1.5"/>
                            <path d="M15 12l-3-3m3 3l-3 3m3-3h8" stroke-width="1.5"/>
                        </svg>
                        <span class="font-semibold">Logout</span>
                    </a>
                </nav>
            </aside>

            <!-- Main content -->
            <main class="bg-white rounded-l-2xl shadow-lg ring-1 ring-stone-200 p-6 lg:p-8 h-full overflow-y-auto">
                <!-- Top bar -->
                @php
                    $pageTitle = 'Dashboard';
                    if (Route::is('admin.models') || Route::is('super.models')) $pageTitle = 'Data Model Rambut';
                    elseif (Route::is('admin.vitamins') || Route::is('super.vitamins')) $pageTitle = 'Data Vitamin Rambut';
                    elseif (Route::is('admin.reports') || Route::is('super.reports')) $pageTitle = 'Analitik & Laporan';
                    elseif (Route::is('super.admins')) $pageTitle = 'Manajemen Admin';
                    elseif (Route::is('super.profile')) $pageTitle = 'Profil';
                @endphp
                <header class="sticky top-0 z-40">
                    <div class="rounded-2xl bg-gradient-to-r from-pink-50 to-amber-50 ring-1 ring-pink-200 px-4 py-3 flex items-center justify-between gap-4 shadow-sm">
                        <!-- Left: Breadcrumb + title -->
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="h-8 w-8 grid place-items-center rounded-lg bg-pink-50 ring-1 ring-pink-200 text-pink-600">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4"><path d="M3 11l9-7 9 7" stroke-width="1.5"/><path d="M5 11v9h14v-9" stroke-width="1.5"/></svg>
                            </div>
                            <div class="truncate">
                                <p class="text-[11px] text-stone-600">Super Admin / <span class="text-stone-700">{{ $pageTitle }}</span></p>
                                <p class="text-sm font-semibold truncate">{{ $pageTitle }}</p>
                            </div>
                        </div>

                        <!-- Middle: Quick search -->
                        <div class="hidden md:flex flex-1 max-w-md items-center gap-2 rounded-2xl ring-1 ring-pink-200 bg-white/70 px-3 py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 text-stone-500"><circle cx="11" cy="11" r="7" stroke-width="1.5"/><path d="M20 20l-3-3" stroke-width="1.5"/></svg>
                            <input id="nav-search" type="text" placeholder="Cari di halaman ini..." class="w-full bg-transparent outline-none text-xs" autocomplete="off" />
                        </div>

                        <!-- Right: actions + avatar -->
                        <div class="flex items-center gap-3 relative">
                            <!-- Mobile hamburger (only on small screens) -->
                            <button id="mobile-menu-btn" class="lg:hidden h-10 w-10 grid place-items-center rounded-lg ring-1 ring-pink-200 bg-white/80 text-pink-600 focus:outline-none" aria-label="Buka menu">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="1.5"/></svg>
                            </button>
                            <button id="avatar-btn" class="h-10 w-10 rounded-full overflow-hidden ring-2 ring-pink-200 focus:outline-none">
                                <img src="{{ $avatarUrl }}" alt="Avatar" class="h-full w-full object-cover" />
                            </button>
                            <div id="avatar-dropdown" class="absolute right-0 top-12 w-40 bg-white rounded-xl shadow-lg ring-1 ring-stone-200 py-2 hidden">
                                <a href="{{ route('super.profile') }}" class="block px-3 py-2 text-sm hover:bg-pink-50">Edit Profil</a>
                            </div>
                        </div>
                    </div>
                    <!-- Mobile dropdown nav -->
                    <div id="mobile-menu" class="lg:hidden mt-2 rounded-2xl bg-white ring-1 ring-stone-200 shadow hidden">
                        <nav class="py-2">
                            <a href="{{ route('super.dashboard') }}" class="flex items-center gap-3 px-4 py-2 text-sm {{ Route::is('super.dashboard') ? 'bg-pink-50 text-stone-900' : 'hover:bg-pink-50' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 text-pink-600"><path d="M3 11l9-7 9 7" stroke-width="1.5"/><path d="M5 11v9h14v-9" stroke-width="1.5"/></svg>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('super.models') }}" class="flex items-center gap-3 px-4 py-2 text-sm {{ Route::is('super.models') ? 'bg-pink-50 text-stone-900' : 'hover:bg-pink-50' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 text-pink-600"><circle cx="6" cy="7" r="2.5" stroke-width="1.5"/><circle cx="6" cy="17" r="2.5" stroke-width="1.5"/><path d="M8 8l12 8" stroke-width="1.5"/><path d="M8 16l12-8" stroke-width="1.5"/></svg>
                                <span>Data Model Rambut</span>
                            </a>
                            <a href="{{ route('super.vitamins') }}" class="flex items-center gap-3 px-4 py-2 text-sm {{ Route::is('super.vitamins') ? 'bg-pink-50 text-stone-900' : 'hover:bg-pink-50' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 text-pink-600"><rect x="4" y="6" width="16" height="12" rx="6" ry="6" stroke-width="1.5"/><path d="M12 7v10" stroke-width="1.5"/></svg>
                                <span>Data Vitamin Rambut</span>
                            </a>
                            <a href="{{ route('super.reports') }}" class="flex items-center gap-3 px-4 py-2 text-sm {{ Route::is('super.reports') ? 'bg-pink-50 text-stone-900' : 'hover:bg-pink-50' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 text-pink-600"><path d="M4 20h16" stroke-width="1.5"/><rect x="6" y="11" width="3" height="7" rx="1" stroke-width="1.5"/><rect x="11" y="8" width="3" height="10" rx="1" stroke-width="1.5"/><rect x="16" y="13" width="3" height="5" rx="1" stroke-width="1.5"/></svg>
                                <span>Analitik & Laporan</span>
                            </a>
                            <a href="{{ route('super.admins') }}" class="flex items-center gap-3 px-4 py-2 text-sm {{ Route::is('super.admins') ? 'bg-pink-50 text-stone-900' : 'hover:bg-pink-50' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 text-pink-600"><path d="M16 11c1.657 0 3-1.567 3-3.5S17.657 4 16 4s-3 1.567-3 3.5 1.343 3.5 3 3.5Z" stroke-width="1.5"/><path d="M8 11c1.657 0 3-1.567 3-3.5S9.657 4 8 4 5 5.567 5 7.5 6.343 11 8 11Z" stroke-width="1.5"/></svg>
                                <span>Manajemen Admin</span>
                            </a>
                            <div class="my-2 border-t border-stone-200"></div>
                            <a href="{{ route('super.profile') }}" class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-pink-50">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 text-pink-600"><circle cx="12" cy="8" r="3" stroke-width="1.5"/><path d="M4 20v-1c0-2.5 4-4 8-4s8 1.5 8 4v1" stroke-width="1.5"/></svg>
                                <span>Edit Profil</span>
                            </a>
                            <a href="{{ route('super.logout') }}" class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-pink-50">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 text-pink-600"><path d="M10 5H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h4" stroke-width="1.5"/><path d="M15 12l-3-3m3 3l-3 3m3-3h8" stroke-width="1.5"/></svg>
                                <span>Logout</span>
                            </a>
                        </nav>
                    </div>
                </header>

                <section class="mt-6">
                    @yield('content')
                </section>
            </main>
        </div>
        <script>
            // Navbar quick search -> forward to page search inputs
            document.addEventListener('DOMContentLoaded', function(){
                const nav = document.getElementById('nav-search');
                if (!nav) return;

                const targets = [
                    document.getElementById('rec-search'),
                    document.getElementById('vitamin-search'),
                    document.getElementById('model-search'),
                ].filter(Boolean);

                function forward(val){
                    targets.forEach(t => {
                        t.value = val;
                        t.dispatchEvent(new Event('input', { bubbles: true }));
                    });
                }

                // Initialize nav value from first available target
                const first = targets[0];
                if (first && first.value) nav.value = first.value;

                nav.addEventListener('input', (e) => forward(e.target.value));

                // Avatar dropdown toggle
                const btn = document.getElementById('avatar-btn');
                const dd = document.getElementById('avatar-dropdown');
                btn?.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dd?.classList.toggle('hidden');
                });
                document.addEventListener('click', (e) => {
                    if (!dd) return;
                    const within = btn?.contains(e.target) || dd?.contains(e.target);
                    if (!within) dd.classList.add('hidden');
                });

                // Mobile menu toggle
                const mbtn = document.getElementById('mobile-menu-btn');
                const mm = document.getElementById('mobile-menu');
                mbtn?.addEventListener('click', (e) => {
                    e.stopPropagation();
                    mm?.classList.toggle('hidden');
                });
                document.addEventListener('click', (e) => {
                    if (!mm) return;
                    const within = mbtn?.contains(e.target) || mm?.contains(e.target);
                    if (!within) mm.classList.add('hidden');
                });
            });
        </script>
    </body>
</html>