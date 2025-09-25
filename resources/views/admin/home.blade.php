{{-- resources/views/users/home.blade.php --}}
@extends('layout.app')

@section('title', 'Home')

@section('content')
    <div class="text-center">
        <h2 class="text-3xl font-extrabold text-white mb-4">
            Selamat Datang, {{ $user->name ?? 'Admin' }}!
        </h2>
        <p class="text-gray-400 mb-6">
            Ini adalah halaman Home sederhana. Kamu bisa mengubah kontennya sesuai kebutuhan.
        </p>
        <a href="{{ url('/dashboard') }}"
            class="inline-block bg-indigo-600 text-white font-semibold px-6 py-2 rounded-lg shadow hover:bg-indigo-500 transition">
            Pergi ke Dashboard
        </a>
    </div>

    <section class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-white mb-2">Fitur MEMEK LOER1</h3>
            <p class="text-gray-400 text-sm">Deskripsi singkat tentang fitur pertama.</p>
        </div>
        <div class="bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-white mb-2">Fitur 2</h3>
            <p class="text-gray-400 text-sm">Deskripsi singkat tentang fitur kedua.</p>
        </div>
        <div class="bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-white mb-2">Fitur 3</h3>
            <p class="text-gray-400 text-sm">Deskripsi singkat tentang fitur ketiga.</p>
        </div>
    </section>
@endsection
