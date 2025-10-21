@extends('layouts.admin', ['title' => 'Manajemen Akomodasi'])

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
@endpush

@section('content')
    <div x-data="{
        modalOpen: false,
        isEditMode: false,
        modalTitle: '',
        formAction: '',
        formData: {},
        deleteModalOpen: false,
        accommodationToDelete: {},
        imagePreviewUrl: null,
        errors: { name: null, partner_id: null },
        modalSelectInstances: {},
        filterSelectInstances: {},
        statusModalOpen: false,
        accommodationToUpdateStatus: {},
        statusFormAction: '',
        newStatus: '',
    
        initModalSelects() {
            Object.values(this.modalSelectInstances).forEach(select => select && select.destroy());
            const config = { create: false, sortField: { field: 'text', direction: 'asc' } };
            
            try { 
                const elPartner = document.getElementById('partner_id');
                if (elPartner) this.modalSelectInstances.partner = new TomSelect(elPartner, { ...config, placeholder: 'Cari & pilih partner...' }); 
                else console.warn('Element #partner_id not found for TomSelect');
            } catch (e) { console.error('Error init partner select:', e); }
            
            try { 
                const elDest = document.getElementById('destination_id');
                if (elDest) this.modalSelectInstances.destination = new TomSelect(elDest, { ...config, placeholder: 'Cari tempat wisata...' }); 
                else console.warn('Element #destination_id not found for TomSelect');
            } catch (e) { console.error('Error init destination select:', e); }
        },
    
        initFilterSelects() {
            Object.values(this.filterSelectInstances).forEach(select => select && select.destroy());
            try { this.filterSelectInstances.destination = new TomSelect('#filter_destination_select', { create: false, placeholder: 'Cari destinasi...' }); } catch (e) {}
            try { this.filterSelectInstances.type = new TomSelect('#filter_type_select', { create: false, placeholder: 'Pilih tipe...' }); } catch (e) {}
            this.filterSelectsInitialized = true;
        },
    
        openCreateModal() {
            this.isEditMode = false; this.modalTitle = 'Tambah Akomodasi Baru';
            this.formAction = '{{ route("admin.managements.accommodations.store") }}';
            this.formData = { name: '', partner_id: '', destination_id: '', type: '', address: '', description: '', status: 'pending', is_verified: false }; 
            this.imagePreviewUrl = null; this.errors = { name: null, partner_id: null }; this.modalOpen = true;
            this.$nextTick(() => this.initModalSelects());
        },
        openEditModal(accommodationData) {
            this.isEditMode = true; this.modalTitle = 'Edit Akomodasi: ' + accommodationData.name;
            this.formAction = `/admin/managements/accommodations/${accommodationData.slug}`;
            this.formData = { ...accommodationData, is_verified: Boolean(accommodationData.is_verified) }; 
            this.imagePreviewUrl = accommodationData.media && accommodationData.media.length > 0 ? `{{ asset('storage') }}/${accommodationData.media[0].file_path}` : null;
            this.errors = { name: null, partner_id: null }; this.modalOpen = true;
            this.$nextTick(() => { 
                this.initModalSelects();
                if (this.formData.partner_id && this.modalSelectInstances.partner) this.modalSelectInstances.partner.setValue(this.formData.partner_id, true);
                if (this.formData.destination_id && this.modalSelectInstances.destination) this.modalSelectInstances.destination.setValue(this.formData.destination_id, true);
            });
        },
        openDeleteModal(accommodationData) {
            this.accommodationToDelete = accommodationData;
            this.deleteModalOpen = true;
        },
        openStatusModal(accommodationData) {
            this.accommodationToUpdateStatus = accommodationData;
            this.statusFormAction = `{{ url('admin/managements/accommodations') }}/${accommodationData.slug}/status`;
            this.newStatus = accommodationData.status;
            this.statusModalOpen = true;
        },
        previewImage(event) {
            const file = event.target.files[0];
            if (file) { this.imagePreviewUrl = URL.createObjectURL(file); } else if (this.isEditMode && this.formData.media && this.formData.media.length > 0) {
                this.imagePreviewUrl = `{{ asset('storage') }}/${this.formData.media[0].file_path}`;
            } else { this.imagePreviewUrl = null; }
        },
    
        validate() {
            this.errors = {};
            let isValid = true;
            if (!this.formData.name) {
                this.errors.name = 'Nama akomodasi wajib diisi.';
                isValid = false;
            }
            if (!this.formData.partner_id) {
                this.errors.partner_id = 'Partner wajib dipilih.';
                isValid = false;
            }
            return isValid;
        },
        handleSubmit() { 
            this.formData.partner_id = this.modalSelectInstances.partner ? this.modalSelectInstances.partner.getValue() : '';
            this.formData.destination_id = this.modalSelectInstances.destination ? this.modalSelectInstances.destination.getValue() : '';
            
            if (this.validate()) { 
                this.formData.is_verified = this.$refs.is_verified_checkbox ? this.$refs.is_verified_checkbox.checked : false; // Tambahkan pengecekan ref
                this.$nextTick(() => { this.$refs.form.submit(); }); 
            } 
        }
    }" class="mt-8">
        {{-- Header & Tombol Tambah --}}
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-700">Filter & Kelola Akomodasi</h3>
            <button @click="openCreateModal()"
                class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Akomodasi
            </button>
        </div>
        {{-- FORM FILTER (Sesuaikan filter) --}}
        <div class="bg-white p-4 rounded-2xl shadow-lg mb-6" x-init="initFilterSelects()">
            <form action="{{ route('admin.managements.accommodations.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input type="text" name="search" placeholder="Cari nama akomodasi..."
                        value="{{ request('search') }}"
                        class="block w-full pl-4 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    <div>
                        <div x-show="!filterSelectsInitialized" class="select-placeholder"></div>
                        <select name="filter_destination" id="filter_destination_select" x-show="filterSelectsInitialized"
                            x-cloak>
                            <option value="">Semua Destinasi</option>
                            @foreach ($destinations as $destination)
                                <option value="{{ $destination->id }}"
                                    {{ request('filter_destination') == $destination->id ? 'selected' : '' }}>
                                    {{ $destination->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <div x-show="!filterSelectsInitialized" class="select-placeholder"></div>
                        <select name="filter_type" id="filter_type_select" x-show="filterSelectsInitialized" x-cloak>
                            <option value="">Semua Tipe</option>
                            @foreach ($types as $type)
                                <option value="{{ $type }}" {{ request('filter_type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button type="submit"
                            class="w-full inline-flex justify-center py-2 px-4 border rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Filter</button>
                        <a href="{{ route('admin.managements.accommodations.index') }}"
                            class="w-full inline-flex justify-center py-2 px-4 border rounded-md text-gray-700 bg-white hover:bg-gray-50">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        {{-- TABEL AKOMODASI (Sesuaikan kolom) --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase w-16">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Akomodasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Partner</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Destinasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Verifikasi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($accommodations as $accommodation)
                        <tr>
                            <td class="px-6 py-4 text-center">
                                <div
                                    class="flex items-center justify-center w-6 h-6 bg-indigo-600 text-white rounded-full text-xs font-bold">
                                    {{ $loop->iteration }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-20 w-22">
                                        <img class="h-20 w-32 rounded-lg object-cover"
                                            src="{{ $accommodation->media->first() ? asset('storage/' . $accommodation->media->first()->file_path) : 'https://placehold.co/400x400/EBF4FF/76879D?text=IMG' }}"
                                            alt="{{ $accommodation->name }}">
                                    </div>
                                    <div class="ml-4 flex flex-col gap-2">
                                        <div>
                                            <span
                                                class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                {{ $accommodation->category->name }}
                                            </span>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">{{ $accommodation->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500">{{ $accommodation->partner->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500">{{ $accommodation->destination->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500">{{ ucfirst($accommodation->type) }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button @click="openStatusModal({{ json_encode($accommodation) }})" type="button"
                                    class="focus:outline-none">
                                    @if ($accommodation->status == 'publish')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 cursor-pointer hover:bg-green-200">Publish</span>
                                    @elseif ($accommodation->status == 'pending')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 cursor-pointer hover:bg-yellow-200">Pending</span>
                                    @elseif ($accommodation->status == 'rejected')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 cursor-pointer hover:bg-red-200">Rejected</span>
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 cursor-pointer hover:bg-gray-200">Draft</span>
                                    @endif
                                </button>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($accommodation->is_verified)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 cursor-pointer hover:bg-blue-300">Verified</span>
                                @else
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 cursor-pointer hover:bg-gray-300">Not
                                        Verified</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center items-center space-x-2">
                                    <button @click="openEditModal({{ json_encode($accommodation) }})"
                                        class="text-indigo-600 hover:text-indigo-900 p-1" title="Edit"><svg
                                            class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg></button>
                                    <button @click="openDeleteModal({{ json_encode($accommodation) }})"
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
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">Tidak ada data akomodasi yang
                                tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6 mb-4">{{ $accommodations->appends(request()->query())->links() }}</div>

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
                        {{-- Nama Paket --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Akomodasi</label>
                            <input type="text" name="name" id="name" x-model="formData.name"
                                @input="errors.name = null"
                                :class="{ 'border-red-500': errors.name, 'border-gray-300': !errors.name }"
                                placeholder="Contoh: Tur Sunrise Bromo 2 Hari 1 Malam"
                                class="mt-1 block w-full bg-gray-50 px-4 py-2 border rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <p x-show="errors.name" x-text="errors.name" x-transition class="mt-1 text-sm text-red-600">
                            </p>
                        </div>

                        {{-- Dropdown Partner --}}
                        <div>
                            <label for="partner_id" class="block text-sm font-medium text-gray-700">Milik Partner</label>
                            <select name="partner_id" id="partner_id" @input="errors.partner_id = null"
                                :class="{ 'border-red-500': errors.partner_id }">
                                <option value="">Pilih Partner</option> {{-- <-- TAMBAHKAN INI --}}
                                @foreach ($partners as $partner)
                                    <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                @endforeach
                            </select>
                            <p x-show="errors.partner_id" x-text="errors.partner_id" x-transition
                                class="mt-1 text-sm text-red-600"></p>
                        </div>

                        {{-- Dropdown Destinasi & Kategori --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="destination_id" class="block text-sm font-medium text-gray-700">Tujuan
                                    Destinasi</label>
                                <select name="destination_id" id="destination_id"
                                    @input="errors.destination_id = null"
                                    :class="{ 'border-red-500': errors.destination_id }">
                                    <option value="">Pilih Tempat Wisata</option> {{-- <-- TAMBAHKAN INI --}}
                                    @foreach ($destinations as $destination)
                                        <option value="{{ $destination->id }}">{{ $destination->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Tipe
                                    Akomodasi</label>
                                <select name="type" id="type" x-model="formData.type"
                                    class="mt-1 block w-full bg-gray-50 pl-4 pr-10 py-2 border text-base shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm rounded-md">
                                    <option value="">Pilih Tipe</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                            <textarea name="address" id="address" rows="4" x-model="formData.address"
                                placeholder="Daftarkan alamat akomodasi agar mudah diakses..."
                                class="mt-1 block w-full bg-gray-50 px-4 py-2 border rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi
                                Akomodasi</label>
                            <textarea name="description" id="description" rows="4" x-model="formData.description"
                                placeholder="Jelaskan detail paket, apa saja yang termasuk, dan jadwal perjalanannya..."
                                class="mt-1 block w-full bg-gray-50 px-4 py-2 border rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-6 items-center">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status
                                    Akomodasi</label>
                                <select name="status" id="status" x-model="formData.status"
                                    class="mt-1 block w-full bg-gray-50 pl-4 pr-10 py-2 border text-base shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm rounded-md">
                                    <option value="pending">Pending</option>
                                    <option value="publish">Publish</option>
                                    <option value="draft">Draft</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                            <div class="pt-6">
                                <label class="flex items-center space-x-2 cursor-pointer select-none">
                                    <input type="checkbox" name="is_verified" value="1"
                                        x-model="formData.is_verified" x-ref="is_verified_checkbox"
                                        class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 cursor-pointer">
                                    <span class="text-sm font-medium text-gray-700">Terverifikasi oleh <span
                                            class="text-indigo-600 font-semibold">Travora</span></span>
                                </label>
                            </div>

                        </div>

                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">Gambar Utama
                                Akomodasi</label>
                            <input type="file" name="image" id="image" @change="previewImage($event)"
                                accept="image/*"
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
                            <span x-text="isEditMode ? 'Simpan Perubahan' : 'Simpan Akomodasi'"></span>
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
                        Anda yakin ingin menghapus akomodasi <strong class="font-medium text-gray-800"
                            x-text="accommodationToDelete.name"></strong>?
                        <br>
                        <span class="font-medium text-red-600">Tindakan ini tidak dapat dibatalkan.</span>
                    </p>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-x-4 rounded-b-2xl">
                    <button @click="deleteModalOpen = false" type="button"
                        class="px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase bg-white hover:bg-gray-50">
                        Batal
                    </button>
                    <form :action="`/admin/managements/accommodations/${accommodationToDelete.slug}`" method="POST">
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

        <div x-show="statusModalOpen" x-cloak x-transition.opacity.duration.300ms
            class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
            <div @click.away="statusModalOpen = false" class="bg-white rounded-2xl shadow-lg w-full max-w-md">
                <div class="p-5 flex items-center bg-indigo-600 rounded-t-2xl">
                    <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center bg-white/20 rounded-full">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <h3 class="ml-4 text-xl font-semibold text-white">Ubah Status Akomodasi</h3>
                </div>

                <form :action="statusFormAction" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" :value="newStatus">

                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800">Ubah Status Akomodasi</h3>
                        <p class="text-sm text-gray-600 mt-1">Akomodasi: <strong x-text="accommodationToUpdateStatus.name"></strong>
                        </p>

                        <div class="mt-4 space-y-2">
                            <button @click.prevent="newStatus = 'publish'" type="button"
                                class="w-full flex items-center p-3 rounded-lg border transition-colors"
                                :class="newStatus === 'publish' ? 'bg-green-50 border-green-300' :
                                    'bg-white hover:bg-gray-50'">
                                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                                <span class="ml-3 text-sm font-medium text-gray-700">Publish</span>
                                <svg x-show="newStatus === 'publish'" class="w-5 h-5 ml-auto text-green-600"
                                    fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                            <button @click.prevent="newStatus = 'pending'" type="button"
                                class="w-full flex items-center p-3 rounded-lg border transition-colors"
                                :class="newStatus === 'pending' ? 'bg-yellow-50 border-yellow-300' :
                                    'bg-white hover:bg-gray-50'">
                                <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                                <span class="ml-3 text-sm font-medium text-gray-700">Pending</span>
                                <svg x-show="newStatus === 'pending'" class="w-5 h-5 ml-auto text-yellow-600"
                                    fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                            <button @click.prevent="newStatus = 'draft'" type="button"
                                class="w-full flex items-center p-3 rounded-lg border transition-colors"
                                :class="newStatus === 'draft' ? 'bg-gray-50 border-gray-400' : 'bg-white hover:bg-gray-50'">
                                <span class="w-3 h-3 rounded-full bg-gray-500"></span>
                                <span class="ml-3 text-sm font-medium text-gray-700">Draft</span>
                                <svg x-show="newStatus === 'draft'" class="w-5 h-5 ml-auto text-gray-600" fill="none"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                            <button @click.prevent="newStatus = 'rejected'" type="button"
                                class="w-full flex items-center p-3 rounded-lg border transition-colors"
                                :class="newStatus === 'rejected' ? 'bg-red-50 border-red-300' : 'bg-white hover:bg-gray-50'">
                                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                                <span class="ml-3 text-sm font-medium text-gray-700">Rejected</span>
                                <svg x-show="newStatus === 'rejected'" class="w-5 h-5 ml-auto text-red-600"
                                    fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-x-4 rounded-b-2xl">
                        <button @click="statusModalOpen = false" type="button"
                            class="px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase bg-white hover:bg-gray-50">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700">
                            Simpan Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
@endpush
