@extends('layouts.admin', ['title' => 'Manajemen Paket Wisata'])
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
        packageToDelete: {},
        imagePreviewUrl: null,
        errors: {},
        modalSelectInstances: {},
        filterSelectInstances: {},
        statusModalOpen: false,
        packageToUpdateStatus: {},
        statusFormAction: '',
        newStatus: '',
        viewPackageModalOpen: false,
        packageToView: {},
    
        initModalSelects() {
            Object.values(this.modalSelectInstances).forEach(select => select && select.destroy());
            const config = { create: false, sortField: { field: 'text', direction: 'asc' } };
    
            try { this.modalSelectInstances.partner = new TomSelect('#partner_id', { ...config, placeholder: 'Cari & pilih partner...' }); } catch (e) { console.error('Error init partner select:', e); }
            try { this.modalSelectInstances.destination = new TomSelect('#destination_id', { ...config, placeholder: 'Cari tempat wisata...' }); } catch (e) { console.error('Error init destination select:', e); }
            try { this.modalSelectInstances.category = new TomSelect('#category_id', { ...config, placeholder: 'Cari kategori...' }); } catch (e) { console.error('Error init category select:', e); }
        },
    
        initFilterSelects() {
            Object.values(this.filterSelectInstances).forEach(select => select && select.destroy());
            try {
                const elFilterDest = document.getElementById('filter_destination_select');
                if (elFilterDest) this.filterSelectInstances.destination = new TomSelect(elFilterDest, { create: false, placeholder: 'Cari destinasi...' });
            } catch (e) {}
            try {
                const elFilterCat = document.getElementById('filter_category_select'); 
                if (elFilterCat) this.filterSelectInstances.category = new TomSelect(elFilterCat, { create: false, placeholder: 'Cari kategori...' });
            } catch (e) {}
            this.filterSelectsInitialized = true;
        },
    
        openCreateModal() {
            this.isEditMode = false;
            this.modalTitle = 'Tambah Paket Wisata Baru';
            this.formAction = '{{ route('admin.managements.packages.store') }}';
            this.formData = { name: '', partner_id: '', destination_id: '', category_id: '', duration_days: '', price: '', description: '', status: 'pending' };
            this.imagePreviewUrl = null;
            this.errors = {};
            this.modalOpen = true;
            this.$nextTick(() => this.initModalSelects());
        },
    
        openCreateModal() {
            this.isEditMode = false;
            this.modalTitle = 'Tambah Paket Baru';
            this.formAction = '{{ route('admin.managements.packages.store') }}';
            this.formData = { name: '', partner_id: '', destination_id: '', category_id: '', duration_days: '', price: '', description: '', status: 'pending' };
            this.imagePreviewUrl = null;
            this.errors = {};
            this.modalOpen = true;
            this.$nextTick(() => this.initModalSelects());
        },
    
        openEditModal(packageData) {
            this.isEditMode = true;
            this.modalTitle = 'Edit Paket: ' + packageData.name;
            this.formAction = `/admin/managements/packages/${packageData.slug}`;
            this.formData = { ...packageData };
            if (this.formData.price) {
                this.formData.price = parseInt(this.formData.price);
            }
            this.imagePreviewUrl = packageData.hero_image_url ? `{{ asset('storage') }}/${packageData.hero_image_url}` : null;
            this.errors = {};
            this.modalOpen = true;
            this.$nextTick(() => {
                this.initModalSelects();
                if (this.formData.partner_id && this.modalSelectInstances.partner) this.modalSelectInstances.partner.setValue(this.formData.partner_id);
                if (this.formData.destination_id && this.modalSelectInstances.destination) this.modalSelectInstances.destination.setValue(this.formData.destination_id);
                if (this.formData.category_id && this.modalSelectInstances.category) this.modalSelectInstances.category.setValue(this.formData.category_id);
            });
        },
    
        openDeleteModal(packageData) {
            this.packageToDelete = packageData;
            this.deleteModalOpen = true;
        },
    
        openStatusModal(packageData) {
            this.packageToUpdateStatus = packageData;
            this.statusFormAction = `{{ url('admin/managements/packages') }}/${packageData.slug}/status`;
            this.newStatus = packageData.status;
            this.statusModalOpen = true;
        },
    
        openViewPackageModal(packageData) {
            this.packageToView = packageData; 
            if (packageData.created_at) {
                this.packageToView.created_at_formatted = new Date(packageData.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            }
            if (packageData.updated_at) {
                this.packageToView.updated_at_formatted = new Date(packageData.updated_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            }
            this.viewPackageModalOpen = true;
        },
    
        previewImage(event) {
            const file = event.target.files[0];
            if (file) { this.imagePreviewUrl = URL.createObjectURL(file); } else if (this.isEditMode && this.formData.media && this.formData.media.length > 0) {
                this.imagePreviewUrl = `{{ asset('storage') }}/${this.formData.media[0].file_path}`;
            } else {
                this.imagePreviewUrl = null;
            }
        },
    
        validate() {
            this.errors = {};
            let isValid = true;
            const requiredFields = {
                name: 'Nama paket wajib diisi.',
                partner_id: 'Partner wajib dipilih.',
                destination_id: 'Tujuan destinasi wajib dipilih.',
                category_id: 'Kategori wajib dipilih.',
                duration_days: 'Durasi wajib diisi.',
                price: 'Harga wajib diisi.',
                description: 'Deskripsi wajib diisi.',
                status: 'Status wajib dipilih.'
            };
    
            for (const field in requiredFields) {
                if (!this.formData[field]) {
                    this.errors[field] = requiredFields[field];
                    isValid = false;
                }
            }
    
            if (this.formData.duration_days && (isNaN(this.formData.duration_days) || this.formData.duration_days < 1)) {
                this.errors.duration_days = 'Durasi harus berupa angka minimal 1.';
                isValid = false;
            }
            if (this.formData.price && (isNaN(this.formData.price) || this.formData.price < 0)) {
                this.errors.price = 'Harga harus berupa angka positif.';
                isValid = false;
            }
    
            return isValid;
        },
    
        handleSubmit() {
            this.formData.partner_id = this.modalSelectInstances.partner ? this.modalSelectInstances.partner.getValue() : '';
            this.formData.destination_id = this.modalSelectInstances.destination ? this.modalSelectInstances.destination.getValue() : '';
            this.formData.category_id = this.modalSelectInstances.category ? this.modalSelectInstances.category.getValue() : '';
    
            if (this.validate()) {
                this.$nextTick(() => {
                    this.$refs.form.submit();
                });
            }
        }
    }" class="mt-8">

        <div class="bg-white p-4 rounded-2xl shadow-lg mb-6" x-init="initFilterSelects()">
            <form action="{{ route('admin.managements.packages.index') }}" method="GET">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-4">
                    <div class="relative flex-grow w-full md:w-auto">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" placeholder="Cari nama paket wisata..."
                            value="{{ $requestInput['search'] ?? '' }}"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    </div>
                    <div class="flex items-center space-x-2 w-full md:w-auto flex-shrink-0">
                        <button type="submit"
                            class="w-1/2 md:w-auto inline-flex justify-center py-2 px-5 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Filter</button>
                        <a href="{{ route('admin.managements.packages.index') }}"
                            class="w-1/2 md:w-auto inline-flex justify-center py-2 px-5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Reset</a>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-8 gap-4">
                    <div x-show="!filterSelectsInitialized" class="select-placeholder"></div>
                    <select name="filter_destination" id="filter_destination_select" x-show="filterSelectsInitialized" x-cloak
                        class="col-span-2 md:col-span-3 shadow-md">
                        <option value="">Semua Destinasi</option>
                        @foreach ($destinations as $destination)
                            <option value="{{ $destination->slug }}"
                                {{ ($requestInput['filter_destination'] ?? '') == $destination->slug ? 'selected' : '' }}>
                                {{ $destination->name }}
                            </option>
                        @endforeach
                    </select>

                    <div x-show="!filterSelectsInitialized" class="select-placeholder"></div>
                    <select name="filter_category" id="filter_category_select" x-show="filterSelectsInitialized" x-cloak class="md:col-span-2 shadow-md">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}"
                                {{ ($requestInput['filter_category'] ?? '') == $category->slug ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="filter_status" id="filter_status_select"
                        class="md:col-span-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-sm shadow-md focus:outline-none text-xs text-gray-600 appearance-none">
                        <option value="">Semua Status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}"
                                {{ ($requestInput['filter_status'] ?? '') == $status ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>

                    <select name="sort_by"
                        class="md:col-span-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-sm shadow-md focus:outline-none text-xs text-gray-600 appearance-none">
                        <option value="default"
                            {{ ($requestInput['sort_by'] ?? 'default') == 'default' ? 'selected' : '' }}>Urutkan (Default)
                        </option>
                        <option value="name" {{ ($requestInput['sort_by'] ?? '') == 'name' ? 'selected' : '' }}>Nama
                            Paket</option>
                        <option value="price" {{ ($requestInput['sort_by'] ?? '') == 'price' ? 'selected' : '' }}>Harga
                        </option>
                        <option value="status" {{ ($requestInput['sort_by'] ?? '') == 'status' ? 'selected' : '' }}>Status
                        </option>
                    </select>

                    <select name="direction"
                        class="md:col-span-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-sm shadow-md focus:outline-none text-xs text-gray-600 appearance-none">
                        <option value="desc" {{ ($requestInput['direction'] ?? 'desc') == 'desc' ? 'selected' : '' }}>
                            Menurun</option>
                        <option value="asc" {{ ($requestInput['direction'] ?? '') == 'asc' ? 'selected' : '' }}>Menaik
                        </option>
                    </select>
                </div>


            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase w-16">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paket</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Partner</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Destinasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durasi (hari)</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($packages as $package)
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
                                            src="{{ $package->media->first() ? asset('storage/' . $package->media->first()->file_path) : 'https://placehold.co/400x400/EBF4FF/76879D?text=IMG' }}"
                                            alt="{{ $package->name }}">
                                    </div>
                                    <div class="ml-4 flex flex-col gap-2">
                                        <div>
                                            <span
                                                class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                {{ $package->category->name }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $package->name }}</div>
                                            <div class="text-sm text-gray-500">Rp
                                                {{ number_format($package->price, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500">{{ $package->partner->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500">{{ $package->destination->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500">{{ $package->duration_days }} hari</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button @click="openStatusModal({{ json_encode($package) }})" type="button"
                                    class="focus:outline-none">
                                    @if ($package->status == 'publish')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 cursor-pointer hover:bg-green-200">Publish</span>
                                    @elseif ($package->status == 'pending')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 cursor-pointer hover:bg-yellow-200">Pending</span>
                                    @elseif ($package->status == 'rejected')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 cursor-pointer hover:bg-red-200">Rejected</span>
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 cursor-pointer hover:bg-gray-200">Draft</span>
                                    @endif
                                </button>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center items-center space-x-2">
                                    <button @click="openViewPackageModal({{ json_encode($package) }})"
                                        class="text-blue-600 hover:text-blue-900 p-1" title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button @click="openDeleteModal({{ json_encode($package) }})"
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
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">Tidak ada data paket yang
                                tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($packages->isNotEmpty())
                <div
                    class="px-4 py-6 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <form action="{{ route('admin.managements.packages.index') }}" method="GET"
                        class="flex items-center space-x-2">
                        @foreach (request()->except(['perPage', 'page']) as $key => $value)
                            @if (is_array($value))
                                @foreach ($value as $item)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach

                        <label for="perPage" class="text-sm font-medium text-gray-700 whitespace-nowrap">Tampil:</label>
                        <select name="perPage" id="perPage" @change="$el.closest('form').submit()"
                            class="appearance-none block w-20 py-1.5 px-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span class="text-sm text-gray-500">data</span>
                    </form>
                    <div>
                        {{ $packages->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
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
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Paket</label>
                            <input type="text" name="name" id="name" x-model="formData.name"
                                @input="errors.name = null"
                                :class="{ 'border-red-500': errors.name, 'border-gray-300': !errors.name }"
                                placeholder="Contoh: Tur Sunrise Bromo 2 Hari 1 Malam"
                                class="mt-1 block w-full bg-gray-50 px-4 py-2 border rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <p x-show="errors.name" x-text="errors.name" x-transition class="mt-1 text-sm text-red-600">
                            </p>
                        </div>

                        <div>
                            <label for="partner_id" class="block text-sm font-medium text-gray-700">Milik Partner</label>
                            <select name="partner_id" id="partner_id">
                                <option value="">Pilih Partner</option> 
                                @foreach ($partners as $partner)
                                    <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                @endforeach
                            </select>
                            <p x-show="errors.partner_id" x-text="errors.partner_id" x-transition
                                class="mt-1 text-sm text-red-600"></p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="destination_id" class="block text-sm font-medium text-gray-700">Tujuan
                                    Destinasi</label>
                                <select name="destination_id" id="destination_id">
                                    <option value="">Pilih Tempat Wisata</option> 
                                    @foreach ($destinations as $destination)
                                        <option value="{{ $destination->id }}">{{ $destination->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori
                                    Paket</label>
                                <select name="category_id" id="category_id">
                                    <option value="">Pilih Kategori</option> 
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="duration_days" class="block text-sm font-medium text-gray-700">Durasi
                                    (Hari)</label>
                                <input type="number" name="duration_days" id="duration_days"
                                    x-model="formData.duration_days" placeholder="Contoh: 3"
                                    class="mt-1 block w-full bg-gray-50 px-4 py-2 border rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="relative">
                                <label for="price" class="block text-sm font-medium text-gray-700">Harga Paket</label>
                                <div class="absolute inset-y-0 left-0 top-6 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" name="price" id="price" x-model="formData.price"
                                    placeholder="1500000"
                                    class="mt-1 block w-full bg-gray-50 pl-10 px-4 py-2 border rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Paket &
                                Itinerary</label>
                            <textarea name="description" id="description" rows="4" x-model="formData.description"
                                placeholder="Jelaskan detail paket, apa saja yang termasuk, dan jadwal perjalanannya..."
                                class="mt-1 block w-full bg-gray-50 px-4 py-2 border rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status Paket</label>
                            <select name="status" id="status" x-model="formData.status"
                                class="mt-1 block w-full bg-gray-50 pl-4 pr-10 py-2 border text-base shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm rounded-md">
                                <option value="pending">Pending</option>
                                <option value="publish">Publish</option>
                                <option value="draft">Draft</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">Gambar Utama
                                Paket</label>
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
                            <span x-text="isEditMode ? 'Simpan Perubahan' : 'Simpan Paket'"></span>
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
                    <form :action="`/admin/managements/packages/${packageToDelete.slug}`" method="POST">
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
                    <h3 class="ml-4 text-xl font-semibold text-white">Ubah Status Paket</h3>
                </div>

                <form :action="statusFormAction" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" :value="newStatus">

                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800">Ubah Status Paket</h3>
                        <p class="text-sm text-gray-600 mt-1">Paket: <strong x-text="packageToUpdateStatus.name"></strong>
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
        <div x-show="viewPackageModalOpen" x-cloak x-transition.opacity.duration.300ms
            class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
            <div @click.away="viewPackageModalOpen = false"
                class="bg-white rounded-2xl shadow-lg w-full max-w-xl max-h-[90vh] flex flex-col overflow-hidden">
                <div class="p-5 bg-blue-600 rounded-t-2xl">
                    <div class="flex items-center">
                        <div class="flex items-center">
                            <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center bg-white/20 rounded-full">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <h3 class="ml-4 text-xl font-semibold text-white">Detail Paket Wisata</h3>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-4 overflow-y-auto">
                    <img :src="packageToView.media && packageToView.media.length > 0 ?
                        `{{ asset('storage') }}/${packageToView.media[0].file_path}` : 'https://placehold.co/600x300'"
                        :alt="packageToView.name" class="w-full h-48 object-cover rounded-lg border shadow-sm mb-4">

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div>
                            <dt class="font-medium text-gray-500">Nama Paket</dt>
                            <dd class="mt-1 font-semibold text-lg text-gray-900" x-text="packageToView.name || '-'"></dd>

                            <dt class="mt-3 font-medium text-gray-500">Slug URL</dt>
                            <dd class="mt-1 text-gray-700 font-mono text-xs bg-gray-100 px-2 py-1 rounded inline-block"
                                x-text="packageToView.slug || '-'"></dd>

                            <dt class="mt-3 font-medium text-gray-500">Partner Penyedia</dt>
                            <dd class="mt-1 text-gray-900"
                                x-text="packageToView.partner ? packageToView.partner.name : '-'"></dd>

                            <dt class="mt-3 font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span x-show="packageToView.status == 'publish'"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                                <span x-show="packageToView.status == 'pending'"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                <span x-show="packageToView.status == 'rejected'"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                                <span x-show="packageToView.status == 'draft'"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                            </dd>

                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Destinasi Tujuan</dt>
                            <dd class="mt-1 text-gray-900"
                                x-text="packageToView.destination ? packageToView.destination.name : '-'"></dd>

                            <dt class="mt-3 font-medium text-gray-500">Durasi</dt>
                            <dd class="mt-1 text-gray-900"><span x-text="packageToView.duration_days || '0'"></span> Hari
                            </dd>

                            <dt class="mt-3 font-medium text-gray-500">Harga</dt>
                            <dd class="mt-1 text-lg font-semibold text-indigo-600">Rp <span
                                    x-text="packageToView.price ? Number(packageToView.price).toLocaleString('id-ID') : '0'"></span>
                            </dd>

                            <dt class="mt-3 font-medium text-gray-500">Kategori</dt>
                            <dd class="mt-1 text-gray-900"
                                x-text="packageToView.category ? packageToView.category.name : '-'"></dd>
                        </div>

                        <div class="sm:col-span-2">
                            <dt class="font-medium text-gray-500">Deskripsi & Itinerary</dt>
                            <dd class="mt-1 text-gray-700 whitespace-pre-wrap" x-text="packageToView.description || '-'">
                            </dd>
                        </div>

                        <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 pt-3 border-t">
                            <div>
                                <dt class="font-medium text-gray-500">Tanggal Dibuat</dt>
                                <dd class="mt-1 text-gray-900" x-text="packageToView.created_at_formatted || '-'"></dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Terakhir Diperbarui</dt>
                                <dd class="mt-1 text-gray-900" x-text="packageToView.updated_at_formatted || '-'"></dd>
                            </div>
                        </div>
                    </dl>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex justify-end rounded-b-2xl flex-shrink-0">
                    <button @click="viewPackageModalOpen = false" type="button"
                        class="px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase bg-white hover:bg-gray-50">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endsection
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                setTimeout(() => {
                    Alpine.store('alpineData').initFilterSelects();
                }, 100);
            });
        </script>
    @endpush
