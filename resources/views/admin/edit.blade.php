@extends('layout.app')

@section('title', 'Edit Member')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4 text-2xl font-bold text-white">Edit Member</h1>

        @if ($errors->any())
            <div class="mb-4 text-red-400">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.update', $member->id) }}" method="POST" class="p-4 bg-gray-800 rounded">
            @csrf
            @method('PUT')

            <input type="text" name="name" value="{{ $member->name }}" placeholder="Nama" class="mb-2 p-2 rounded w-full"
                required>
            <input type="email" name="email" value="{{ $member->email }}" placeholder="Email"
                class="mb-2 p-2 rounded w-full" required>
            <select name="kelas_id" class="mb-2 p-2 rounded w-full" required>
                <option value="">-- Pilih Kelas --</option>
                @foreach ($kelas as $k)
                    <option value="{{ $k->id }}" {{ $member->kelas_id == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>
            <input type="password" name="password" placeholder="Password Baru (opsional)" class="mb-2 p-2 rounded w-full">
            <input type="password" name="password_confirmation" placeholder="Konfirmasi Password Baru"
                class="mb-2 p-2 rounded w-full">

            <button type="submit" class="bg-indigo-500 px-4 py-2 rounded text-white hover:bg-indigo-400">Update
                Member</button>
            <a href="{{ route('users.manage') }}"
                class="ml-2 px-4 py-2 rounded bg-gray-500 text-white hover:bg-gray-400">Batal</a>
        </form>
    </div>
@endsection
