<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Admin Dashboard - E-Monev' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans bg-gray-100">
        <div class="flex h-screen bg-gray-100">
            <!-- Sidebar -->
            <aside class="w-64 bg-white shadow-md hidden md:block flex flex-col">
                <div>
                    <div class="p-6">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                            <img src="/images/logobna.png" alt="Logo E-Monev" class="h-10 w-auto">
                            <span class="text-xl font-bold text-gray-800">E-Monev</span>
                        </a>
                    </div>
                    
                    {{-- Menambahkan padding horizontal dan vertikal untuk memberi jarak antar menu --}}
                    <nav class="mt-6 px-4 space-y-2">
                        {{-- Helper variables to check the current route --}}
                        @php
                            $isDashboard = request()->routeIs('admin.dashboard');
                            $isKuesioner = request()->routeIs('admin.kuesioner*');
                            $isPenilaian = request()->routeIs('admin.penilaian*');
                            $isBadanPublik = request()->routeIs('admin.badan-publik*');
                            $isLaporan = request()->routeIs('admin.laporan*');
                            $isPesan = request()->routeIs('admin.pesan*');
                            $isPengaturan = request()->routeIs('admin.pengaturan*');
                            $isKeluar = request()->routeIs('admin.keluar*'); // Tambahkan ini
                        @endphp

                        {{-- Menu Dashboard --}}
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg {{ $isDashboard ? 'text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900' }}">
                            @if ($isDashboard)
                                <img src="{{ asset('images/icons/dashboard-active.png') }}" alt="Dashboard Icon" class="w-6 h-6">
                            @else
                                <img src="{{ asset('images/icons/dashboard.png') }}" alt="Dashboard Icon" class="w-6 h-6">
                            @endif
                            <span class="ml-4">Dashboard</span>
                        </a>

                        {{-- Menu Kuesioner --}}
                        <a href="{{ route('admin.kuesioner') }}" class="flex items-center px-4 py-3 rounded-lg {{ $isKuesioner ? 'text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900' }}">
                            @if ($isKuesioner)
                                <img src="{{ asset('images/icons/kuesioner-active.png') }}" alt="Kuesioner Icon" class="w-6 h-6">
                            @else
                                <img src="{{ asset('images/icons/kuesioner.png') }}" alt="Kuesioner Icon" class="w-6 h-6">
                            @endif
                            <span class="ml-4">Kuesioner</span>
                        </a>

                        {{-- Menu Penilaian --}}
                        <a href="{{ route('admin.penilaian') }}" class="flex items-center px-4 py-3 rounded-lg {{ $isPenilaian ? 'text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900' }}">
                             @if ($isPenilaian)
                                <img src="{{ asset('images/icons/penilaian-active.png') }}" alt="Penilaian Icon" class="w-6 h-6">
                            @else
                                <img src="{{ asset('images/icons/penilaian.png') }}" alt="Penilaian Icon" class="w-6 h-6">
                            @endif
                            <span class="ml-4">Penilaian</span>
                        </a>

                        {{-- Menu Badan Publik --}}
                        <a href="{{ route('admin.badan-publik') }}" class="flex items-center px-4 py-3 rounded-lg {{ $isBadanPublik ? 'text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900' }}">
                            @if ($isBadanPublik)
                                <img src="{{ asset('images/icons/badanpublik-active.png') }}" alt="Badan Publik Icon" class="w-6 h-6">
                            @else
                                <img src="{{ asset('images/icons/badanpublik.png') }}" alt="Badan Publik Icon" class="w-6 h-6">
                            @endif
                            <span class="ml-4">Badan Publik</span>
                        </a>

                        {{-- Menu Laporan --}}
                        <a href="{{ route('admin.laporan') }}" class="flex items-center px-4 py-3 rounded-lg {{ $isLaporan ? 'text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900' }}">
                            @if ($isLaporan)
                                <img src="{{ asset('images/icons/laporan-active.png') }}" alt="Laporan Icon" class="w-6 h-6">
                            @else
                                <img src="{{ asset('images/icons/laporan.png') }}" alt="Laporan Icon" class="w-6 h-6">
                            @endif
                            <span class="ml-4">Laporan</span>
                        </a>

                        {{-- Menu Pesan --}}
                        <a href="{{ route('admin.pesan') }}" class="flex items-center px-4 py-3 rounded-lg {{ $isPesan ? 'text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900' }}">
                            @if ($isPesan)
                                <img src="{{ asset('images/icons/pesan-active.png') }}" alt="Pesan Icon" class="w-6 h-6">
                            @else
                                <img src="{{ asset('images/icons/pesan.png') }}" alt="Pesan Icon" class="w-6 h-6">
                            @endif
                            <span class="ml-4">Pesan</span>
                        </a>
                        
                        {{-- Menu Pengaturan --}}
                        <a href="{{ route('admin.pengaturan') }}" class="flex items-center px-4 py-3 rounded-lg {{ $isPengaturan ? 'text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900' }}">
                            @if ($isPengaturan)
                                <img src="{{ asset('images/icons/pengaturan-active.png') }}" alt="Pengaturan Icon" class="w-6 h-6">
                            @else
                                <img src="{{ asset('images/icons/pengaturan.png') }}" alt="Pengaturan Icon" class="w-6 h-6">
                            @endif
                            <span class="ml-4">Pengaturan</span>
                        </a>
                    </nav>
                </div>

                <!-- Menu Keluar (di luar <nav>) -->
                <div class="mt-auto px-4 pb-4">
                    <a href="{{ route('admin.keluar') }}" wire:navigate class="flex items-center px-4 py-3 rounded-lg {{ $isKeluar ? 'text-red-600 bg-red-50' : 'text-gray-500 hover:bg-gray-100 hover:text-red-600' }}">
                        @if ($isKeluar)
                            <img src="{{ asset('images/icons/keluar-active.png') }}" alt="Keluar Icon" class="w-6 h-6">
                        @else
                            <img src="{{ asset('images/icons/keluar.png') }}" alt="Keluar Icon" class="w-6 h-6">
                        @endif
                        <span class="ml-4">Keluar</span>
                    </a>
                </div>
            </aside>

            <!-- Main content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <header class="flex justify-between items-center py-4 px-6 bg-white border-b-2 border-gray-200">
                    <div class="flex items-center">
                        {{ $header ?? '' }}
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-medium text-gray-700">{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</span>
                        
                        @if(Auth::guard('admin')->user()->profile_photo_path)
                            <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . Auth::guard('admin')->user()->profile_photo_path) }}?v={{ time() }}" alt="Foto Profil">
                        @else
                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                        @endif
                    </div>
                </header>
                <main class="flex-1 flex flex-col overflow-x-hidden overflow-y-auto bg-gray-100">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
