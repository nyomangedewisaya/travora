<aside
    class="flex-shrink-0 bg-indigo-800 text-white w-64 flex-col transition-all duration-300 ease-in-out z-30 fixed inset-y-0 left-0 md:relative md:flex"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    <div class="h-20 flex items-center justify-center bg-indigo-800 flex-shrink-0">
        <img src="{{ asset('storage/travora-logo-white.jpeg') }}" alt="travora">
    </div>

    <nav class="mt-2 flex-grow overflow-y-auto">
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center mt-2 py-3 px-6 border-l-4 transition-all duration-200
            {{ request()->routeIs('admin.dashboard') 
                ? 'bg-indigo-700 border-indigo-300 text-white' 
                : 'border-transparent text-indigo-100 hover:bg-indigo-700 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                </path>
            </svg>
            <span class="mx-3 font-medium">Dashboard</span>
        </a>

        <h3 class="mt-5 px-6 text-xs uppercase text-indigo-300 font-semibold tracking-wider">Manajemen</h3>

        <a href="{{ route('admin.managements.destinations.index') }}"
            class="flex items-center mt-2 py-3 px-6 border-l-4 transition-all duration-200
            {{ request()->routeIs('admin.managements.destinations.*') 
                ? 'bg-indigo-700 border-indigo-300 text-white' 
                : 'border-transparent text-indigo-100 hover:bg-indigo-700 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="mx-3">Destinasi</span>
        </a>

        <a href="{{ route('admin.managements.packages.index') }}"
            class="flex items-center mt-2 py-3 px-6 border-l-4 transition-all duration-200
            {{ request()->routeIs('admin.managements.packages.*') 
                ? 'bg-indigo-700 border-indigo-300 text-white' 
                : 'border-transparent text-indigo-100 hover:bg-indigo-700 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                </path>
            </svg>
            <span class="mx-3">Paket Wisata</span>
        </a>

        <a href="{{ route('admin.managements.accommodations.index') }}"
            class="flex items-center mt-2 py-3 px-6 border-l-4 transition-all duration-200
            {{ request()->routeIs('admin.managements.accommodations.index') 
                ? 'bg-indigo-700 border-indigo-300 text-white' 
                : 'border-transparent text-indigo-100 hover:bg-indigo-700 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                </path>
            </svg>
            <span class="mx-3">Akomodasi</span>
        </a>

        <a href="{{ route('admin.managements.accommodations.rooms.index') }}"
            class="flex items-center mt-2 py-3 px-6 border-l-4 transition-all duration-200
            {{ request()->routeIs('admin.managements.accommodations.rooms.*') 
                ? 'bg-indigo-700 border-indigo-300 text-white' 
                : 'border-transparent text-indigo-100 hover:bg-indigo-700 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                </path>
            </svg>
            <span class="mx-3">Kamar Akomodasi</span>
        </a>

        <a href="#"
            class="flex items-center mt-2 py-3 px-6 border-l-4 transition-all duration-200
            {{ request()->routeIs('admin.orders.*') 
                ? 'bg-indigo-700 border-indigo-300 text-white' 
                : 'border-transparent text-indigo-100 hover:bg-indigo-700 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
            </svg>
            <span class="mx-3">Pesanan</span>
        </a>

        <h3 class="mt-5 px-6 text-xs uppercase text-indigo-300 font-semibold tracking-wider">Pengguna</h3>

        <a href="#"
            class="flex items-center mt-2 py-3 px-6 border-l-4 transition-all duration-200
            {{ request()->routeIs('admin.partners.*') 
                ? 'bg-indigo-700 border-indigo-300 text-white' 
                : 'border-transparent text-indigo-100 hover:bg-indigo-700 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                </path>
            </svg>
            <span class="mx-3">Partner</span>
        </a>

        <a href="#"
            class="flex items-center mt-2 py-3 px-6 border-l-4 transition-all duration-200
            {{ request()->routeIs('admin.customers.*') 
                ? 'bg-indigo-700 border-indigo-300 text-white' 
                : 'border-transparent text-indigo-100 hover:bg-indigo-700 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197M15 11a3 3 0 11-6 0 3 3 0 016 0z">
                </path>
            </svg>
            <span class="mx-3">Pelanggan</span>
        </a>
    </nav>

    <div class="px-6 py-4 bg-indigo-900">
        <form method="POST" action="{{ route('auth.logout') }}">
            @csrf
            <button type="submit" class="w-full text-left flex items-center text-indigo-200 hover:text-red-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                </svg>
                <span class="mx-3">Keluar</span>
            </button>
        </form>
    </div>
</aside>

<div x-show="sidebarOpen" @click="sidebarOpen = false"
     class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden" style="display: none;">
</div>
