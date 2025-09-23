<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TrainerController extends Controller
{
    /**
     * Tampilkan daftar semua trainer.
     */
    public function index()
    {
        $trainers = Trainer::latest()->get();
        return view('admin.trainers.index', compact('trainers'));
    }

    /**
     * Tampilkan form tambah trainer (jika kamu pakai halaman terpisah).
     */
    public function create()
    {
        return view('admin.trainers.create');
    }

    /**
     * Simpan data trainer baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:trainers,email',
            'password'       => 'required|min:6',
            'phone'          => 'nullable|string|max:20',
            'specialization' => 'nullable|string|max:255',
        ]);

        Trainer::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'phone'          => $request->phone,
            'specialization' => $request->specialization,
            'is_active'      => true,
        ]);

        return redirect()->route('trainers.index')->with('success', 'Trainer berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail / form edit trainer.
     */
    public function edit(Trainer $trainer)
    {
        return view('admin.trainers.edit', compact('trainer'));
    }

    /**
     * Update data trainer.
     */
    public function update(Request $request, Trainer $trainer)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:trainers,email,' . $trainer->id,
            'password'       => 'nullable|min:6',
            'phone'          => 'nullable|string|max:20',
            'specialization' => 'nullable|string|max:255',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'specialization']);

        // Hash password hanya jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $trainer->update($data);

        return redirect()->route('trainers.index')->with('success', 'Trainer berhasil diperbarui.');
    }

    /**
     * Hapus data trainer.
     */
    public function destroy(Trainer $trainer)
    {
        $trainer->delete();
        return redirect()->route('trainers.index')->with('success', 'Trainer berhasil dihapus.');
    }
}
