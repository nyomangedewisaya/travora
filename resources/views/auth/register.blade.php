@extends('layouts.auth', ['title' => 'Create Account'])
@section('content')
    <div x-data="{ role: 'customer' }">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Create Your Account</h2>
        <p class="text-center text-gray-500 mb-6">Let's get you started!</p>

        <div class="grid grid-cols-2 gap-2 p-1 bg-gray-100 rounded-lg mb-6">
            <button type="button" @click="role = 'customer'"
                :class="{ 'bg-emerald-600 text-white shadow': role === 'customer', 'text-gray-600': role !== 'customer' }"
                class="w-full py-2 text-sm font-medium rounded-md transition-colors duration-300 focus:outline-none">
                As a Customer
            </button>
            <button type="button" @click="role = 'partner'"
                :class="{ 'bg-emerald-600 text-white shadow': role === 'partner', 'text-gray-600': role !== 'partner' }"
                class="w-full py-2 text-sm font-medium rounded-md transition-colors duration-300 focus:outline-none">
                As a Partner
            </button>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route('auth.register') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="role" :value="role">
            <div class="space-y-4">
                <input type="text" name="name" placeholder="Full Name" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-500 focus:border-emerald-500" value="{{ old('name') }}">
                <input type="email" name="email" placeholder="Email Address" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-500 focus:border-emerald-500" value="{{ old('email') }}">
                <input type="tel" name="phone_number" placeholder="Phone Number" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-500 focus:border-emerald-500" value="{{ old('phone_number') }}">
                <input type="password" name="password" placeholder="Password" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">
                <input type="password" name="password_confirmation" placeholder="Confirm Password" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">
                
                <div x-show="role === 'partner'" x-transition>
                    <label for="profile_photo_path" class="block text-sm font-medium text-gray-700 mb-1">Profile Photo (Required for Partners)</label>
                    <input type="file" name="profile_photo_path" id="profile_photo_path"
                        class="block w-full text-sm text-gray-500
                               file:mr-4 file:py-2 file:px-4
                               file:rounded-md file:border-0
                               file:text-sm file:font-semibold
                               file:bg-emerald-50 file:text-emerald-700
                               hover:file:bg-emerald-100">
                </div>
                
                <div>
                    <button type="submit"
                        class="w-full mt-2 flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-300">
                        Create Account
                    </button>
                </div>
            </div>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            Already have an account?
            <a href="{{ route('auth.login') }}" class="font-medium text-emerald-600 hover:underline">Sign In</a>
        </p>
    </div>
@endsection