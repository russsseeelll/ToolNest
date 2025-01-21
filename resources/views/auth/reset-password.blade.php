@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
    <main class="container mx-auto py-12 px-6 flex justify-center">
        <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-2xl font-bold text-[#003865] mb-6 text-center">Reset Password</h2>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-4 text-green-700 bg-green-100 border border-green-300 rounded-lg p-4">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 text-red-700 bg-red-100 border border-red-300 rounded-lg p-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003865]" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" name="password" id="password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003865]" required>
                </div>
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003865]" required>
                </div>
                <div class="flex justify-center">
                    <button type="submit" class="bg-[#003865] text-white px-6 py-2 rounded-lg hover:bg-[#002a52]">Reset Password</button>
                </div>
            </form>
        </div>
    </main>
@endsection
