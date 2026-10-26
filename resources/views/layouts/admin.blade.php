<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#f8fafc]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - RT/RW Digital</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Chart.js CDN untuk grafik interaktif -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
    </style>
</head>
<body class="h-full text-slate-800 antialiased selection:bg-blue-600 selection:text-white" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen flex flex-col lg:flex-row bg-[#f8fafc]">

        <!-- Mobile Top Navbar (Hanya di Layar Kecil) -->
        <header class="lg:hidden bg-[#1d4ed8] text-white px-4 py-3 flex items-center justify-between sticky top-0 z-40 shadow-sm">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <div>
                    <span class="font-extrabold text-white text-xs tracking-tight uppercase">RT/RW Digital</span>
                    <span class="text-[9px] font-semibold text-blue-200 block -mt-0.5 tracking-wider uppercase">ADMINISTRASI WARGA</span>
                </div>
            </div>
            <button @click="sidebarOpen = true" class="p-2 text-white/90 hover:text-white rounded-lg hover:bg-white/10 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </header>

        <!-- Backdrop Mobile -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition-opacity ease-linear duration-300" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             @click="sidebarOpen = false" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 lg:hidden" 
             style="display: none;"></div>

        <!-- Sidebar Utama Warna Biru Gradien -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" 
               class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-[#1d4ed8] via-[#1e40af] to-[#172554] text-white flex flex-col justify-between transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shrink-0 min-h-screen shadow-xl">
            
            <div class="p-5 overflow-y-auto">
                <!-- Brand Header -->
                <div class="flex items-center justify-between pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-amber-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 3L2 12h3v8h6v-6h2v6h6v-8h3L12 3z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-black text-white text-base tracking-tight leading-tight">RT/RW Digital</div>
                            <div class="text-[9px] font-bold text-blue-200 tracking-wider uppercase leading-tight">ADMINISTRASI WARGA</div>
                        </div>
                    </div>

                    <!-- Tombol tutup di mobile -->
                    <button @click="sidebarOpen = false" class="lg:hidden text-white/70 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- User Profile Card -->
                <div class="bg-white/10 rounded-2xl p-3 flex items-center gap-3 backdrop-blur-sm border border-white/10 mt-1 mb-5">
                    <div class="w-9 h-9 rounded-full bg-white/25 text-white font-black flex items-center justify-center text-sm shadow-inner shrink-0">
                        {{ strtoupper(substr(Auth::user()->name ?? 'P', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="font-bold text-white text-xs truncate leading-snug">
                            {{ Auth::user()->name ?? 'Pengurus RT 01' }}
                        </div>
                        <div class="text-[10px] font-semibold text-blue-200 uppercase tracking-wider leading-none mt-0.5">
                            {{ Auth::user()->role ?? 'ADMIN' }}
                        </div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="space-y-4">
                    <!-- SECTION: UTAMA -->
                    <div>
                        <span class="text-[10px] font-bold text-blue-200/70 uppercase tracking-widest px-2 mb-1.5 block">
                            UTAMA
                        </span>
                        <div class="space-y-1">
                            <!-- Dashboard -->
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 text-white font-bold shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10 font-medium' }} text-xs rounded-xl transition">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-blue-200' }} shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                                </svg>
                                <span>Dashboard</span>
                            </a>

                            <!-- Data KK -->
                            <a href="{{ route('admin.kk.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('admin.kk*') ? 'bg-white/20 text-white font-bold shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10 font-medium' }} text-xs rounded-xl transition">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.kk*') ? 'text-white' : 'text-blue-200' }} shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Data KK</span>
                            </a>

                            <!-- Data Warga -->
                            <a href="{{ route('admin.warga.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('admin.warga*') ? 'bg-white/20 text-white font-bold shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10 font-medium' }} text-xs rounded-xl transition">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.warga*') ? 'text-white' : 'text-blue-200' }} shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span>Data Warga</span>
                            </a>
                        </div>
                    </div>

                    <!-- SECTION: KEUANGAN -->
                    <div>
                        <span class="text-[10px] font-bold text-blue-200/70 uppercase tracking-widest px-2 mb-1.5 block">
                            KEUANGAN
                        </span>
                        <div class="space-y-1">
                            <!-- Iuran & Tagihan -->
                            <a href="{{ route('admin.iuran.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('admin.iuran*') ? 'bg-white/20 text-white font-bold shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10 font-medium' }} text-xs rounded-xl transition">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.iuran*') ? 'text-white' : 'text-blue-200' }} shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                <span>Iuran & Tagihan</span>
                            </a>

                            <!-- Pembayaran -->
                            <a href="{{ route('admin.pembayaran.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('admin.pembayaran*') ? 'bg-white/20 text-white font-bold shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10 font-medium' }} text-xs rounded-xl transition">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.pembayaran*') ? 'text-white' : 'text-blue-200' }} shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                <span>Pembayaran</span>
                            </a>

                            <!-- Kas Masuk -->
                            <a href="{{ route('admin.kas-masuk.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('admin.kas-masuk*') ? 'bg-white/20 text-white font-bold shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10 font-medium' }} text-xs rounded-xl transition">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.kas-masuk*') ? 'text-white' : 'text-blue-200' }} shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z"/>
                                </svg>
                                <span>Kas Masuk</span>
                            </a>

                            <!-- Kas Keluar -->
                            <a href="{{ route('admin.kas-keluar.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('admin.kas-keluar*') ? 'bg-white/20 text-white font-bold shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10 font-medium' }} text-xs rounded-xl transition">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.kas-keluar*') ? 'text-white' : 'text-blue-200' }} shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z"/>
                                </svg>
                                <span>Kas Keluar</span>
                            </a>
                        </div>
                    </div>

                    <!-- SECTION: ADMINISTRASI -->
                    <div>
                        <span class="text-[10px] font-bold text-blue-200/70 uppercase tracking-widest px-2 mb-1.5 block">
                            ADMINISTRASI
                        </span>
                        <div class="space-y-1">
                            <!-- Surat -->
                            <a href="{{ route('admin.surat.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('admin.surat*') ? 'bg-white/20 text-white font-bold shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10 font-medium' }} text-xs rounded-xl transition">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.surat*') ? 'text-white' : 'text-blue-200' }} shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span>Surat</span>
                            </a>

                            <!-- Pengaduan -->
                            <a href="{{ route('admin.pengaduan.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('admin.pengaduan*') ? 'bg-white/20 text-white font-bold shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10 font-medium' }} text-xs rounded-xl transition">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.pengaduan*') ? 'text-white' : 'text-blue-200' }} shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <span>Pengaduan</span>
                            </a>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Tombol Keluar (Pinned Bottom) -->
            <div class="p-5 pt-0">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 text-white/80 hover:text-white hover:bg-white/10 font-semibold text-xs rounded-xl transition">
                        <svg class="w-4 h-4 text-blue-200 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Area Konten Utama -->
        <main class="flex-1 min-w-0 overflow-y-auto">
            {{ $slot }}
        </main>

    </div>
</body>
</html>
