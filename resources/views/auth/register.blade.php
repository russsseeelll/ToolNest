@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <main class="container mx-auto py-12 px-6 flex justify-center">
        <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-2xl font-bold text-[#003865] mb-6 text-center">Register</h2>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-4 text-green-700 bg-green-100 border border-green-300 rounded-lg p-4">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 text-red-700 bg-red-100 border border-red-300 rounded-lg p-4">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="mb-4 text-red-700 bg-red-100 border border-red-300 rounded-lg p-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="fullname" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" name="fullname" id="fullname" value="{{ old('fullname') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003865]" required>
                </div>
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003865]" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003865]" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003865]" required>
                </div>
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003865]" required>
                </div>
                <div class="flex justify-center">
                    <button type="submit" class="bg-[#003865] text-white px-6 py-2 rounded-lg hover:bg-[#002a52]">Register</button>
                </div>
                <div class="text-center mt-4">
                    <span>Already have an account?</span> <a href="{{ route('login') }}" class="text-[#003865] hover:underline">Login</a>
                </div>
            </form>
        </div>
    </main>
@endsection
