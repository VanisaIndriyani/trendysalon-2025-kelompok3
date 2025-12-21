<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin - Trendy Salon</title>
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
                <div class="flex justify-center group">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo Trendy Salon" class="block h-16 sm:h-20 w-auto mx-auto rounded-sm shadow transition-all duration-300 group-hover:scale-110 group-hover:shadow-xl" />
                </div>
                <div class="mt-5 border-t border-stone-200"></div>

                <!-- Profile card -->
                @php
                    $uid = session('admin_user_id');
                    $u = \App\Models\User::find($uid);
                    $displayName = $u?->name ?? 'Admin';
                    $displayEmail = $u?->email ?? 'admin@trendysalon.com';
                    // Gunakan AvatarHelper untuk generate avatar otomatis
                    $avatarUrl = \App\Helpers\AvatarHelper::getAvatarUrl($uid, $displayName, 'admin');
                @endphp
                <div class="mt-5 rounded-xl border border-pink-200 bg-gradient-to-br from-pink-50 to-pink-100/50 p-4 flex items-center gap-3 transition-all duration-300 hover:shadow-md hover:border-pink-300 group">
                    <div class="h-10 w-10 rounded-full overflow-hidden ring-2 ring-pink-200 transition-all duration-300 group-hover:ring-pink-400 group-hover:scale-110">
                        <img src="{{ $avatarUrl }}" alt="Avatar" class="h-full w-full object-cover" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-stone-800">{{ $displayName }}</p>
                        <p class="text-xs text-stone-600">{{ $displayEmail }}</p>
                    </div>
                </div>

                <!-- Nav -->
                <nav class="mt-6 space-y-2">
                    <!-- Profil -->
                   
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" class="relative flex items-center gap-3 rounded-xl px-4 py-3 border transition-all duration-300 {{ Route::is('admin.dashboard') ? 'bg-gradient-to-r from-pink-100 to-pink-50 text-stone-900 border-pink-200 ring-1 ring-pink-200 shadow-sm' : 'border-stone-200 hover:bg-pink-50 hover:border-pink-200 hover:shadow-sm' }} group">
                        @if(Route::is('admin.dashboard'))
                            <span class="absolute left-1 top-1/2 -translate-y-1/2 h-2 w-2 rounded-full bg-pink-500 animate-pulse"></span>
                        @endif
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 text-pink-600 transition-transform duration-300 group-hover:scale-110">
                            <path d="M3 11l9-7 9 7" stroke-width="1.5"/>
                            <path d="M5 11v9h14v-9" stroke-width="1.5"/>
                            <path d="M10 20v-6h4v6" stroke-width="1.5"/>
                        </svg>
                        <span class="font-semibold">Dashboard</span>
                    </a>
                    <!-- Data Model Rambut -->
                    <a href="{{ route('admin.models') }}" class="relative flex items-center gap-3 rounded-xl px-4 py-3 border transition-all duration-300 {{ Route::is('admin.models') ? 'bg-gradient-to-r from-pink-100 to-pink-50 text-stone-900 border-pink-200 ring-1 ring-pink-200 shadow-sm' : 'border-stone-200 hover:bg-pink-50 hover:border-pink-200 hover:shadow-sm' }} group">
                        @if(Route::is('admin.models'))
                            <span class="absolute left-1 top-1/2 -translate-y-1/2 h-2 w-2 rounded-full bg-pink-500 animate-pulse"></span>
                        @endif
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 text-pink-600 transition-transform duration-300 group-hover:scale-110">
                            <circle cx="6" cy="7" r="2.5" stroke-width="1.5"/>
                            <circle cx="6" cy="17" r="2.5" stroke-width="1.5"/>
                            <path d="M8 8l12 8" stroke-width="1.5"/>
                            <path d="M8 16l12-8" stroke-width="1.5"/>
                        </svg>
                        <span class="font-semibold">Data Model Rambut</span>
                    </a>
                    <!-- Data Vitamin Rambut -->
                    <a href="{{ route('admin.vitamins') }}" class="relative flex items-center gap-3 rounded-xl px-4 py-3 border transition-all duration-300 {{ Route::is('admin.vitamins') ? 'bg-gradient-to-r from-pink-100 to-pink-50 text-stone-900 border-pink-200 ring-1 ring-pink-200 shadow-sm' : 'border-stone-200 hover:bg-pink-50 hover:border-pink-200 hover:shadow-sm' }} group">
                        @if(Route::is('admin.vitamins'))
                            <span class="absolute left-1 top-1/2 -translate-y-1/2 h-2 w-2 rounded-full bg-pink-500 animate-pulse"></span>
                        @endif
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 text-pink-600 transition-transform duration-300 group-hover:scale-110">
                            <rect x="4" y="6" width="16" height="12" rx="6" ry="6" stroke-width="1.5"/>
                            <path d="M12 7v10" stroke-width="1.5"/>
                        </svg>
                        <span class="font-semibold">Data Vitamin Rambut</span>
                    </a>
                    <!-- Analitik & Laporan -->
                    <a href="{{ route('admin.reports') }}" class="relative flex items-center gap-3 rounded-xl px-4 py-3 border transition-all duration-300 {{ Route::is('admin.reports') ? 'bg-gradient-to-r from-pink-100 to-pink-50 text-stone-900 border-pink-200 ring-1 ring-pink-200 shadow-sm' : 'border-stone-200 hover:bg-pink-50 hover:border-pink-200 hover:shadow-sm' }} group">
                        @if(Route::is('admin.reports'))
                            <span class="absolute left-1 top-1/2 -translate-y-1/2 h-2 w-2 rounded-full bg-pink-500 animate-pulse"></span>
                        @endif
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 text-pink-600 transition-transform duration-300 group-hover:scale-110">
                            <path d="M4 20h16" stroke-width="1.5"/>
                            <rect x="6" y="11" width="3" height="7" rx="1" stroke-width="1.5"/>
                            <rect x="11" y="8" width="3" height="10" rx="1" stroke-width="1.5"/>
                            <rect x="16" y="13" width="3" height="5" rx="1" stroke-width="1.5"/>
                        </svg>
                        <span class="font-semibold">Analitik & Laporan</span>
                    </a>
                    <!-- Logout -->
                    <a href="{{ route('admin.logout') }}" class="relative flex items-center gap-3 rounded-xl px-4 py-3 border border-stone-200 transition-all duration-300 hover:bg-red-50 hover:border-red-200 hover:shadow-sm group">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 text-red-600 transition-transform duration-300 group-hover:scale-110 group-hover:translate-x-1">
                            <path d="M10 5H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h4" stroke-width="1.5"/>
                            <path d="M15 12l-3-3m3 3l-3 3m3-3h8" stroke-width="1.5"/>
                        </svg>
                        <span class="font-semibold text-red-600">Logout</span>
                    </a>
                </nav>
            </aside>

            <!-- Main content -->
            <main class="bg-white rounded-l-2xl shadow-lg ring-1 ring-stone-200 p-6 lg:p-8 h-full overflow-y-auto">
                <!-- Top bar -->
                @php
                    $pageTitle = 'Dashboard';
                    if (Route::is('admin.models')) $pageTitle = 'Data Model Rambut';
                    elseif (Route::is('admin.vitamins')) $pageTitle = 'Data Vitamin Rambut';
                    elseif (Route::is('admin.reports')) $pageTitle = 'Analitik & Laporan';
                    elseif (Route::is('admin.profile')) $pageTitle = 'Profil';
                @endphp
                <header class="sticky top-0 z-40">
                    <div class="rounded-2xl bg-gradient-to-r from-pink-50 via-pink-100/50 to-amber-50 ring-1 ring-pink-200 px-4 py-3 flex items-center justify-between gap-4 shadow-md hover:shadow-lg transition-all duration-300">
                        <!-- Left: Breadcrumb + title -->
                        <div class="flex items-center gap-3 min-w-0 group">
                            <div class="h-8 w-8 grid place-items-center rounded-lg bg-pink-50 ring-1 ring-pink-200 text-pink-600 transition-all duration-300 group-hover:scale-110 group-hover:bg-pink-100 group-hover:ring-pink-300">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4"><path d="M3 11l9-7 9 7" stroke-width="1.5"/><path d="M5 11v9h14v-9" stroke-width="1.5"/></svg>
                            </div>
                            <div class="truncate">
                                <p class="text-[11px] text-stone-600">Admin / <span class="text-stone-700 font-medium">{{ $pageTitle }}</span></p>
                                <p class="text-sm font-semibold truncate bg-gradient-to-r from-pink-600 to-amber-600 bg-clip-text text-transparent">{{ $pageTitle }}</p>
                            </div>
                        </div>

                        <!-- Middle: Quick search -->
                        <div class="hidden md:flex flex-1 max-w-md items-center gap-2 rounded-2xl ring-1 ring-pink-200 bg-white/70 px-3 py-2 transition-all duration-300 focus-within:ring-2 focus-within:ring-pink-400 focus-within:bg-white hover:ring-pink-300">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 text-stone-500 transition-colors duration-300 focus-within:text-pink-600"><circle cx="11" cy="11" r="7" stroke-width="1.5"/><path d="M20 20l-3-3" stroke-width="1.5"/></svg>
                            <input id="nav-search" type="text" placeholder="Cari di halaman ini..." class="w-full bg-transparent outline-none text-xs transition-colors duration-300 placeholder:text-stone-400 focus:placeholder:text-stone-300" autocomplete="off" />
                        </div>

                        <!-- Right: actions + avatar -->
                        <div class="flex items-center gap-3 relative">
                            <!-- Mobile hamburger (only on small screens) -->
                            <button id="mobile-menu-btn" class="lg:hidden h-10 w-10 grid place-items-center rounded-lg ring-1 ring-pink-200 bg-white/80 text-pink-600 focus:outline-none" aria-label="Buka menu">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="1.5"/></svg>
                            </button>
                            <button id="avatar-btn" class="h-10 w-10 rounded-full overflow-hidden ring-2 ring-pink-200 focus:outline-none transition-all duration-300 hover:ring-pink-400 hover:scale-110 hover:shadow-lg">
                                <img src="{{ $avatarUrl }}" alt="Avatar" class="h-full w-full object-cover" />
                            </button>
                            <div id="avatar-dropdown" class="absolute right-0 top-12 w-40 bg-white rounded-xl shadow-xl ring-1 ring-stone-200 py-2 hidden animate-fade-in-up">
                                <a href="{{ route('admin.profile') }}" class="block px-3 py-2 text-sm hover:bg-pink-50 transition-colors duration-200">Edit Profil</a>
                            </div>
                        </div>
                    </div>
                    <!-- Mobile dropdown nav -->
                    <div id="mobile-menu" class="lg:hidden mt-2 rounded-2xl bg-white ring-1 ring-stone-200 shadow hidden">
                        <nav class="py-2">
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2 text-sm {{ Route::is('admin.dashboard') ? 'bg-pink-50 text-stone-900' : 'hover:bg-pink-50' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 text-pink-600"><path d="M3 11l9-7 9 7" stroke-width="1.5"/><path d="M5 11v9h14v-9" stroke-width="1.5"/></svg>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('admin.models') }}" class="flex items-center gap-3 px-4 py-2 text-sm {{ Route::is('admin.models') ? 'bg-pink-50 text-stone-900' : 'hover:bg-pink-50' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 text-pink-600"><circle cx="6" cy="7" r="2.5" stroke-width="1.5"/><circle cx="6" cy="17" r="2.5" stroke-width="1.5"/><path d="M8 8l12 8" stroke-width="1.5"/><path d="M8 16l12-8" stroke-width="1.5"/></svg>
                                <span>Data Model Rambut</span>
                            </a>
                            <a href="{{ route('admin.vitamins') }}" class="flex items-center gap-3 px-4 py-2 text-sm {{ Route::is('admin.vitamins') ? 'bg-pink-50 text-stone-900' : 'hover:bg-pink-50' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 text-pink-600"><rect x="4" y="6" width="16" height="12" rx="6" ry="6" stroke-width="1.5"/><path d="M12 7v10" stroke-width="1.5"/></svg>
                                <span>Data Vitamin Rambut</span>
                            </a>
                            <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 px-4 py-2 text-sm {{ Route::is('admin.reports') ? 'bg-pink-50 text-stone-900' : 'hover:bg-pink-50' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 text-pink-600"><path d="M4 20h16" stroke-width="1.5"/><rect x="6" y="11" width="3" height="7" rx="1" stroke-width="1.5"/><rect x="11" y="8" width="3" height="10" rx="1" stroke-width="1.5"/><rect x="16" y="13" width="3" height="5" rx="1" stroke-width="1.5"/></svg>
                                <span>Analitik & Laporan</span>
                            </a>
                            <div class="my-2 border-t border-stone-200"></div>
                            <a href="{{ route('admin.profile') }}" class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-pink-50">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 text-pink-600"><circle cx="12" cy="8" r="3" stroke-width="1.5"/><path d="M4 20v-1c0-2.5 4-4 8-4s8 1.5 8 4v1" stroke-width="1.5"/></svg>
                                <span>Edit Profil</span>
                            </a>
                            <a href="{{ route('admin.logout') }}" class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-pink-50">
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
        
        <!-- Enhanced styles with animations -->
        <style>
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .animate-fade-in-up {
                animation: fadeInUp 0.3s ease-out;
            }
            
            /* Smooth transitions */
            * {
                transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            }
        </style>
    </body>
</html>