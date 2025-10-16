<header class="flex justify-between items-center py-4 px-6 bg-white border-b-2 border-slate-200">
    <div class="flex items-center">
        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none md:hidden">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </button>
    </div>

    <div x-data="{ dropdownOpen: false }" class="relative">
        <button @click="dropdownOpen = !dropdownOpen" class="relative flex items-center gap-x-2 focus:outline-none">
            <span class="hidden md:inline text-sm font-medium text-gray-700">Admin</span>
            <div class="relative block h-8 w-8 rounded-full overflow-hidden shadow">
                <img class="h-full w-full object-cover" src="{{ asset('storage/profile-photos/anonym.jpeg') }}" alt="Avatar Anda">
            </div>
        </button>

        <div
            x-show="dropdownOpen"
            @click.away="dropdownOpen = false"
            @keydown.escape.window="dropdownOpen = false"
            class="absolute right-0 mt-2 w-48 bg-white rounded-md overflow-hidden shadow-xl z-10"
            x-transition
            style="display: none;"
        >
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Profil Saya</a>
            <form method="POST" action="{{ route('auth.logout') }}">
                @csrf
                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Keluar</button>
            </form>
        </div>
    </div>
</header>