@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-4">
        <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Pendapatan</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">Rp 120jt</p>
                <p class="text-xs text-green-500 flex items-center mt-1">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    +15% dari bulan lalu
                </p>
            </div>
            <div class="bg-indigo-100 text-indigo-600 p-3 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v.01"></path></svg>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Pesanan Baru</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">34</p>
                <p class="text-xs text-green-500 flex items-center mt-1">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    +5 hari ini
                </p>
            </div>
            <div class="bg-indigo-100 text-indigo-600 p-3 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-lg">...</div>
        <div class="bg-white p-6 rounded-2xl shadow-lg">...</div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="text-xl font-semibold text-gray-700">Grafik Pendapatan</h3>
            <p class="text-gray-500 text-sm mt-1">Menampilkan data 6 bulan terakhir.</p>
            <div class="mt-4 h-64 bg-gray-100 rounded-lg flex items-center justify-center">
                <p class="text-gray-400">Area Grafik</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="text-xl font-semibold text-gray-700">Aktivitas Terbaru</h3>
            <ul class="mt-4 space-y-4">
                <li class="flex items-start">
                    <div class="bg-green-100 text-green-600 p-2 rounded-full mr-3"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Verifikasi Partner <span class="font-bold">Budi Santoso</span> berhasil.</p>
                        <p class="text-xs text-gray-400">2 menit yang lalu</p>
                    </div>
                </li>
                <li class="flex items-start">
                    <div class="bg-yellow-100 text-yellow-600 p-2 rounded-full mr-3"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Paket "Jelajah Bali" perlu approval.</p>
                        <p class="text-xs text-gray-400">1 jam yang lalu</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
@endsection