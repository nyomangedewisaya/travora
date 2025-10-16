@extends('layouts.admin', ['title' => 'Manajemen Destinasi'])
@section('content')
    <div x-data="{
        view: new URLSearchParams(window.location.search).get('view') || 'parents',
        modalOpen: false,
        isEditMode: false,
        modalTitle: '',
        formAction: '',
        formData: {},
        deleteModalOpen: false,
        destinationToDelete: {},
        imagePreviewUrl: null,
        isGeocoding: false,
        errorModalOpen: false,
        errorMessage: '',
        errors: {},
    
        openCreateModal() {
            this.isEditMode = false;
            this.modalTitle = 'Tambah Destinasi Baru';
            this.formAction = '{{ route('admin.managements.destinations.store') }}';
            this.formData = { name: '', parent_id: '', description: '', address: '' };
            this.imagePreviewUrl = null;
            this.modalOpen = true;
        },
        openEditModal(destination) {
            this.isEditMode = true;
            this.modalTitle = 'Edit Destinasi: ' + destination.name;
            this.formAction = `/admin/managements/destinations/${destination.slug}`;
            this.formData = { ...destination };
    
            if (destination.hero_image_url) {
                this.imagePreviewUrl = `{{ asset('storage') }}/${destination.hero_image_url}`;
            } else {
                this.imagePreviewUrl = null;
            }
    
            this.modalOpen = true;
        },
        openDeleteModal(destination) {
            this.destinationToDelete = destination;
            this.deleteModalOpen = true;
        },
    
        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.imagePreviewUrl = URL.createObjectURL(file);
            } else {
                if (!this.formData.hero_image_url) {
                    this.imagePreviewUrl = null;
                }
            }
        },
    
        showErrorModal(message) {
            this.errorMessage = message;
            this.errorModalOpen = true;
        },
    
        async fetchCoordinates() {
            if (!this.formData.address) {
                this.showErrorModal('Mohon isi alamat terlebih dahulu.');
                return;
            }
            this.isGeocoding = true;
            try {
                const response = await fetch(`{{ route('admin.geocode') }}?address=${encodeURIComponent(this.formData.address)}`);
                if (!response.ok) { throw new Error('Alamat tidak ditemukan'); }
                const data = await response.json();
                this.formData.latitude = data.latitude;
                this.formData.longitude = data.longitude;
            } catch (error) {
                this.showErrorModal('Gagal menemukan koordinat. Mohon periksa kembali alamat yang dimasukkan.');
                this.formData.latitude = '';
                this.formData.longitude = '';
            } finally {
                this.isGeocoding = false;
            }
        },
    
        validate() {
            this.errors = {};
            let isValid = true;
            if (!this.formData.name) {
                this.errors.name = 'Nama destinasi wajib diisi.';
                isValid = false;
            }
            if (!this.formData.description) {
                this.errors.description = 'Deskripsi wajib diisi.';
                isValid = false;
            }
            return isValid;
        },
    
        handleSubmit() {
            if (this.validate()) {
                this.$refs.form.submit();
            }
        }
    }" class="mt-8">

        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <div class="grid grid-cols-2 gap-2 p-1 bg-gray-200 rounded-lg w-full sm:w-auto">
                <button type="button" @click="view = 'parents'"
                    :class="{ 'bg-white text-indigo-700 shadow': view === 'parents', 'text-gray-600': view !== 'parents' }"
                    class="px-6 py-2 text-sm font-medium rounded-md transition-colors duration-300 focus:outline-none">Destinasi
                    Induk</button>
                <button type="button" @click="view = 'children'"
                    :class="{ 'bg-white text-indigo-700 shadow': view === 'children', 'text-gray-600': view !== 'children' }"
                    class="px-6 py-2 text-sm font-medium rounded-md transition-colors duration-300 focus:outline-none">Tempat
                    Wisata</button>
            </div>

            <button @click="openCreateModal()"
                class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Destinasi
            </button>
        </div>

        <div x-show="view === 'parents'" x-transition x-cloak>
            <div class="bg-white rounded-2xl shadow-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                                No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                                Wilayah</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jumlah Tempat Wisata</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal Dibuat</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($parentDestinations as $destination)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div
                                        class="flex items-center justify-center w-6 h-6 bg-indigo-600 text-white rounded-full text-xs font-bold">
                                        {{ $loop->iteration }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $destination->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                        {{ $destination->children_count }} Destinasi
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $destination->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center items-center space-x-2">
                                        <button @click="openEditModal({{ json_encode($destination) }})"
                                            class="text-indigo-600 hover:text-indigo-900 p-1" title="Edit"><svg
                                                class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg></button>
                                        <button @click="openDeleteModal({{ json_encode($destination) }})"
                                            class="text-red-600 hover:text-red-900 p-1" title="Hapus"><svg class="w-5 h-5"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">Belum ada data
                                    destinasi induk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="view === 'children'" x-transition x-cloak style="display: none;">
            <div class="bg-white p-4 rounded-2xl shadow-lg mb-6">
                <form action="{{ route('admin.managements.destinations.index') }}" method="GET">
                    <input type="hidden" name="view" value="children">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="relative">
                            <input type="text" name="search" placeholder="Cari nama tempat wisata..."
                                value="{{ request('search') }}"
                                class="block w-full pl-4 pr-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <select name="filter_parent"
                            class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Wilayah Induk</option>
                            @foreach ($parentDestinations as $parent)
                                <option value="{{ $parent->slug }}"
                                    {{ request('filter_parent') == $parent->slug ? 'selected' : '' }}>{{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="flex items-center space-x-2">
                            <button type="submit"
                                class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Filter</button>
                            <a href="{{ route('admin.managements.destinations.index') }}?view=children"
                                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="bg-white rounded-2xl shadow-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase w-16">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Induk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Koordinat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gambar</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($childDestinations as $destination)
                            <tr>
                                <td class="px-6 py-4 text-center">
                                    <div
                                        class="flex items-center justify-center w-6 h-6 bg-indigo-600 text-white rounded-full text-xs font-bold">
                                        {{ $loop->iteration }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $destination->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">{{ $destination->parent->name ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">
                                    @if ($destination->latitude && $destination->longitude)
                                        Lat: {{ $destination->latitude }}
                                        <br>Lng: {{ $destination->longitude }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4"><img
                                        src="{{ $destination->hero_image_url ? asset('storage/' . $destination->hero_image_url) : 'https://placehold.co/400x200' }}"
                                        class="w-32 h-20 object-cover rounded-lg border shadow-sm"
                                        alt="{{ $destination->name }}"></td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <button @click="openEditModal({{ json_encode($destination) }})"
                                            class="text-indigo-600 hover:text-indigo-900 p-1" title="Edit"><svg
                                                class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg></button>
                                        <button @click="openDeleteModal({{ json_encode($destination) }})"
                                            class="text-red-600 hover:text-red-900 p-1" title="Hapus"><svg
                                                class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">Tidak ada data
                                    tempat wisata yang cocok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $childDestinations->appends(request()->query())->links() }}</div>
        </div>

        <div x-show="modalOpen" x-cloak x-transition.opacity.duration.300ms
            class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
            <div @click.away="modalOpen = false"
                class="bg-white rounded-2xl shadow-lg w-full max-w-2xl max-h-[90vh] flex flex-col">
                <div class="p-6 border-b border-gray-200 bg-indigo-700 rounded-t-2xl">
                    <h3 class="text-2xl font-semibold text-white" x-text="modalTitle"></h3>
                </div>
                <form x-ref="form" @submit.prevent="handleSubmit" :action="formAction" method="POST"
                    enctype="multipart/form-data" class="flex-grow flex flex-col overflow-hidden">
                    @csrf
                    <template x-if="isEditMode"><input type="hidden" name="_method" value="PUT"></template>

                    <div class="p-6 space-y-6 overflow-y-auto">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Destinasi</label>
                            <input type="text" name="name" id="name" x-model="formData.name"
                                @input="errors.name = null"
                                :class="{ 'border-red-500': errors.name, 'border-gray-300': !errors.name }"
                                placeholder="Contoh: Gunung Bromo atau Jawa Timur"
                                class="mt-1 block w-full bg-gray-50 px-4 py-2 border rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <p x-show="errors.name" x-text="errors.name" x-transition class="mt-1 text-sm text-red-600">
                            </p>
                        </div>

                        <div>
                            <label for="parent_id" class="block text-sm font-medium text-gray-700">Induk Destinasi
                                (Opsional)</label>
                            <select name="parent_id" id="parent_id" x-model="formData.parent_id"
                                class="mt-1 block w-full bg-gray-50 pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm rounded-md shadow-md">
                                <option value="">-- Tidak Ada Induk (Jadikan sebagai Provinsi/Wilayah) --</option>
                                @foreach ($parentDestinations as $parent)
                                    <option :value="{{ $parent->id }}">{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea name="description" id="description" rows="4" x-model="formData.description"
                                @input="errors.description = null"
                                :class="{ 'border-red-500': errors.description, 'border-gray-300': !errors.description }"
                                placeholder="Jelaskan keunikan destinasi ini..."
                                class="mt-1 block w-full bg-gray-50 px-4 py-2 border rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                            <p x-show="errors.description" x-text="errors.description" x-transition
                                class="mt-1 text-sm text-red-600"></p>
                        </div>
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Alamat atau
                                Lokasi</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input type="text" name="address" id="address" x-model="formData.address"
                                    placeholder="Contoh: Monumen Nasional, Jakarta Pusat"
                                    class="flex-1 block w-full bg-gray-50 shadow-inner rounded-none rounded-l-md px-4 py-2 border-gray-300">
                                <button @click.prevent="fetchCoordinates()" type="button"
                                    class="relative -ml-px inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    <svg x-show="!isGeocoding" class="h-5 w-5 text-gray-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <svg x-show="isGeocoding" class="h-5 w-5 text-indigo-500 animate-spin"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacit y-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span>Cari</span>
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Isi jika ini tempat wisata spesifik, lalu klik "Cari".
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="text-xs text-gray-500">Latitude</label><input type="text"
                                    name="latitude" x-model="formData.latitude" readonly
                                    class="mt-1 block w-full px-3 py-1 border border-gray-200 rounded-md bg-gray-50 text-sm">
                            </div>
                            <div><label class="text-xs text-gray-500">Longitude</label><input type="text"
                                    name="longitude" x-model="formData.longitude" readonly
                                    class="mt-1 block w-full px-3 py-1 border border-gray-200 rounded-md bg-gray-50 text-sm">
                            </div>
                        </div>
                        <div>
                            <label for="hero_image_url" class="block text-sm font-medium text-gray-700">Gambar
                                Utama</label>
                            <input type="file" name="hero_image_url" id="hero_image_url"
                                @change="previewImage($event)" accept="image/*"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="text-xs text-gray-500 mt-1" x-show="isEditMode">Kosongkan jika tidak ingin mengubah
                                gambar.</p>
                        </div>

                        <div x-show="imagePreviewUrl" x-transition class="mt-4">
                            <p class="text-sm font-medium text-gray-500 mb-2">Preview:</p>
                            <img :src="imagePreviewUrl" class="w-full h-48 object-cover rounded-lg border shadow-sm">
                        </div>
                    </div>
                    <div
                        class="px-6 py-4 bg-gray-50 rounded-b-2xl border-t border-gray-200 flex justify-end gap-x-4 flex-shrink-0">
                        <button type="button" @click="modalOpen = false"
                            class="px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase bg-white hover:bg-gray-50">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700">
                            <span x-text="isEditMode ? 'Simpan Perubahan' : 'Simpan Destinasi'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="deleteModalOpen" x-cloak x-transition.opacity.duration.300ms
            class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
            <div @click.away="deleteModalOpen = false" class="bg-white rounded-2xl shadow-lg w-full max-w-md">
                <div class="p-5 flex items-center bg-red-600 rounded-t-2xl">
                    <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center bg-white/20 rounded-full">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <h3 class="ml-4 text-xl font-semibold text-white">Konfirmasi Penghapusan</h3>
                </div>

                <div class="p-6">
                    <p class="text-gray-600">
                        Anda yakin ingin menghapus destinasi <strong class="font-medium text-gray-800"
                            x-text="destinationToDelete.name"></strong>?
                        <br>
                        <span class="font-medium text-red-600">Tindakan ini tidak dapat dibatalkan.</span>
                    </p>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-x-4 rounded-b-2xl">
                    <button @click="deleteModalOpen = false" type="button"
                        class="px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase bg-white hover:bg-gray-50">
                        Batal
                    </button>
                    <form :action="`/admin/managements/destinations/${destinationToDelete.slug}`" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-red-700">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div x-show="errorModalOpen" x-cloak x-transition.opacity.duration.300ms
            class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
            <div @click.away="errorModalOpen = false" class="bg-white rounded-2xl shadow-lg w-full max-w-md">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 mx-auto flex items-center justify-center bg-red-100 rounded-full">
                        <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mt-4">Terjadi Kesalahan</h3>
                    <p class="text-gray-600 mt-2" x-text="errorMessage"></p>
                </div>
                <div class="px-6 py-4 bg-gray-50 rounded-b-2xl border-t border-gray-200 flex justify-end">
                    <button type="button" @click="errorModalOpen = false"
                        class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700">OK</button>
                </div>
            </div>
        </div>
    </div>
@endsection
