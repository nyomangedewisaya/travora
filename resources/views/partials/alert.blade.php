@if (session('success') || session('error'))
    <div
        x-data="{ 
            show: true, 
            progress: 100 
        }"
        x-show="show"
        
        x-init="() => {
            const duration = 5000; 
            const interval = 50;   
            const decrement = 100 * interval / duration;

            let timer = setInterval(() => {
                progress -= decrement;
                if (progress <= 0) {
                    clearInterval(timer);
                    show = false;
                }
            }, interval);
            
            setTimeout(() => show = false, duration);
        }"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-full"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-full"

        class="fixed top-0 left-1/2 -translate-x-1/2 z-50 w-full max-w-sm mt-6"
    >
        <div class="bg-white rounded-xl shadow-2xl flex overflow-hidden">
            
            <div @class([
                'flex items-center justify-center w-16',
                'bg-green-500' => session('success'),
                'bg-red-500' => session('error'),
            ])>
                @if (session('success'))
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                @elseif (session('error'))
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                @endif
            </div>
            
            <div class="flex-grow p-4 relative">
                <p class="font-bold text-gray-800">{{ session('success') ? 'Berhasil!' : 'Terjadi Kesalahan!' }}</p>
                <p class="text-sm text-gray-600">
                    {{ session('success') ?? session('error') }}
                </p>
                
                <button @click="show = false" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
        
        <div class="absolute bottom-0 left-0 w-full h-1 bg-gray-200 rounded-b-xl overflow-hidden">
            <div 
                @class([
                    'h-full transition-all duration-100 ease-linear',
                    'bg-green-500' => session('success'),
                    'bg-red-400' => session('error'),
                ]) 
                :style="`width: ${progress}%`"
            ></div>
        </div>
    </div>
@endif