@extends('layouts.admin', ['title' => 'Manajemen Kamar Akomodasi'])
@section('content')
    <div x-data="{
        deleteModalOpen: false,
        roomToDelete: {}, 
    
        openDeleteModal(roomData) {
            this.roomToDelete = roomData;
            this.deleteModalOpen = true;
        }
    }" class="mt-8">
        @if ($selectedAccommodation)
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <div>
                    <a href="{{ route('admin.managements.accommodations.rooms.index') }}"
                        class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Pilihan Akomodasi
                    </a>
                    <h2 class="text-2xl font-semibold text-gray-800 mt-2">
                        Daftar Kamar: <span class="text-indigo-600">{{ $selectedAccommodation->name }}</span>
                    </h2>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl shadow-lg mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <form action="{{ route('admin.managements.accommodations.rooms.show', $selectedAccommodation->slug) }}"
                        method="GET" class="flex-grow">
                        <input type="hidden" name="perPage" value="{{ $perPage }}">
                        <div class="relative flex-grow w-full md:w-auto">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="search_room" placeholder="Cari nama kamar atau nomor kamar..."
                                value="{{ $requestInput['search_room'] ?? '' }}" 
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-600">
                        </div>
                    </form>
                    <form action="{{ route('admin.managements.accommodations.rooms.show', $selectedAccommodation->slug) }}"
                        method="GET" class="flex items-center space-x-2 flex-shrink-0">
                        <input type="hidden" name="search_room" value="{{ $requestInput['search_room'] ?? '' }}">
                        <label for="perPage" class="text-sm font-medium text-gray-700">Tampil:</label>
                        <select name="perPage" id="perPage" @change="$el.closest('form').submit()"
                            class="appearance-none block w-20 py-1.5 px-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left ...">Kamar</th>
                            <th class="px-6 py-3 text-left ...">No. Kamar</th>
                            <th class="px-6 py-3 text-left ...">Kapasitas</th>
                            <th class="px-6 py-3 text-left ...">Harga / Malam</th>
                            <th class="px-6 py-3 text-left ...">Status</th>
                            <th class="px-6 py-3 text-center ...">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($rooms as $room)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-lg object-cover"
                                                src="{{ $room->media->first() ? asset('storage/' . $room->media->first()->file_path) : 'https://placehold.co/400x400/EBF4FF/76879D?text=IMG' }}"
                                                alt="{{ $room->name }}">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $room->name }}</div>
                                            <div class="text-xs text-gray-500 max-w-xs truncate">
                                                {{ $room->description ?? 'Tidak ada deskripsi' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">{{ $room->room_number ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">{{ $room->capacity }} Orang</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-800">Rp
                                        {{ number_format($room->price_per_night, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($room->status == 'available')
                                        <span class="px-2 ... bg-green-100 text-green-800">Tersedia</span>
                                    @else
                                        <span class="px-2 ... bg-gray-100 text-gray-800">Tidak Tersedia</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button @click="openDeleteModal({{ json_encode($room) }})"
                                        class="text-red-600 hover:text-red-900 p-1" title="Hapus Kamar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada data kamar untuk
                                    akomodasi ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $rooms->appends(request()->query())->links() }}</div>

        @else
            <div class="mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">Pilih Akomodasi</h2>
                <p class="text-gray-500 mt-1">Pilih salah satu akomodasi untuk melihat dan mengelola kamar di dalamnya.</p>
            </div>

            <div class="bg-white p-4 rounded-2xl shadow-lg mb-6">
                <form action="{{ route('admin.managements.accommodations.rooms.index') }}" method="GET"
                    class="flex items-center gap-3">
                    <div class="relative flex-grow w-full md:w-auto">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search_acc" placeholder="Cari nama akomodasi..."
                            value="{{ $requestInput['search_acc'] ?? '' }}"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    </div>
                    <div class="flex items-center space-x-2 w-full md:w-auto flex-shrink-0">
                        <button type="submit"
                            class="w-1/2 md:w-auto inline-flex justify-center py-2 px-5 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Cari</button>
                        <a href="{{ route('admin.managements.accommodations.rooms.index') }}"
                            class="w-1/2 md:w-auto inline-flex justify-center py-2 px-5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Reset</a>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($accommodations as $accommodation)
                    <a href="{{ route('admin.managements.accommodations.rooms.show', $accommodation->slug) }}"
                        class="bg-white rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 group">
                        <div class="h-48 overflow-hidden relative">
                            <img class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                                src="{{ $accommodation->media->first() ? asset('storage/' . $accommodation->media->first()->file_path) : 'https://placehold.co/600x400/EBF4FF/76879D?text=IMG' }}"
                                alt="{{ $accommodation->name }}">
                            <span
                                class="absolute top-3 right-3 px-3 py-1 bg-indigo-600 text-white text-xs font-bold rounded-full shadow-md">
                                {{ $accommodation->rooms_count }} Kamar
                            </span>
                        </div>
                        <div class="p-5">
                            <span
                                class="px-2 py-1 ... bg-indigo-100 text-indigo-800">{{ ucfirst($accommodation->type) }}</span>
                            <h3
                                class="mt-3 text-xl font-semibold text-gray-800 group-hover:text-indigo-600 transition-colors">
                                {{ $accommodation->name }}</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $accommodation->destination->parent->name ?? 'Lokasi tidak diketahui' }}</p>
                        </div>
                    </a>
                @empty
                    <p class="col-span-3 text-center text-gray-500">Tidak ada data akomodasi ditemukan.</p>
                @endforelse
            </div>
            <div class="mt-6">{{ $accommodations->appends(request()->query())->links() }}</div>
        @endif

        <div x-show="deleteModalOpen" x-cloak x-transition.opacity.duration.300ms ...>
            <div @click.away="deleteModalOpen = false" class="bg-white rounded-2xl ..."
                x-transition:enter="ease-out duration-300" ...>
                <div class="p-5 flex items-center bg-red-600 rounded-t-2xl">
                    <div class...><svg ...>/* Ikon Peringatan */</svg></div>
                    <h3 class="ml-4 text-xl font-semibold text-white">Hapus Kamar</h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-600">
                        Anda yakin ingin menghapus kamar <strong class="font-medium text-gray-800"
                            x-text="roomToDelete.name"></strong>?
                        <br>
                        <span class="font-medium text-red-600">Tindakan ini tidak dapat dibatalkan.</span>
                    </p>
                </div>
                <div class="bg-gray-50 px-6 py-4 ...">
                    <button @click="deleteModalOpen = false" ...>Batal</button>
                    <form :action="`/admin/managements/accommodation-rooms/${roomToDelete.id}`" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" ...>Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
