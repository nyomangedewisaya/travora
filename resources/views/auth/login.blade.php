@extends('layouts.auth', ['title' => 'Login to Travora'])
@section('content')
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-1">Welcome Back!</h2>
    <p class="text-center text-gray-500 mb-8">Please enter your details to sign in.</p>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
            <strong class="font-bold">Oops!</strong>
            <span class="block sm:inline">{{ $errors->first('email') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('auth.login') }}">
        @csrf
        <div class="space-y-5">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                                d="M2.003 5.884L10 2.5l7.997 3.384A2 2 0 0019.5 7.5v.5a2 2 0 01-2 2H2.5a2 2 0 01-2-2v-.5a2 2 0 001.503-1.616zM17.5 10.5h.008v5.5a2 2 0 01-2 2H4.5a2 2 0 01-2-2v-5.5h.008l.002-.001.002-.001.006-.002a1 1 0 01.983.985V15h12v-4.5a1 1 0 01.983-.985l.006.002.002.001.002.001z" />
                        </svg>
                    </span>
                    <input type="email" id="email" name="email" required
                        class="mt-1 block w-full border border-gray-300 rounded-md pl-10 pr-4 py-3 shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                        value="{{ old('email') }}" placeholder="you@example.com">
                </div>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <div x-data="{ show: false }" class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                    <input :type="show ? 'text' : 'password'" id="password" name="password" required
                        class="mt-1 block w-full border border-gray-300 rounded-md pl-10 pr-4 py-3 shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="Enter your password">
                    <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274
                                            4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478
                                            0-8.268-2.943-9.542-7a10.05 10.05 0 012.102-3.592m3.174-2.474A9.969
                                            9.969 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.973 9.973 0 01-4.043
                                            5.362M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                        </svg>
                    </button>
                </div>
            </div>
            <div>
                <button type="submit"
                    class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-300">
                    Sign In
                </button>
            </div>
        </div>
    </form>

    <p class="mt-8 text-center text-sm text-gray-500">
        Don't have an account?
        <a href="{{ route('auth.register') }}" class="font-medium text-emerald-600 hover:underline">Sign Up Now</a>
    </p>
@endsection
