<?php

namespace App\Http\Controllers;

use App\Models\Reservasi;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;

class ReservasiController extends Controller
{
    /**
     * Tampilkan semua reservasi
     */
    public function index()
    {
        // Ambil reservasi dengan relasi pelanggan, trainer, dan kelas
        $reservasi = Reservasi::with(['pelanggan', 'trainer', 'kelas'])
            ->latest()
            ->get();

        return view('reservasi.index', compact('reservasi'));
    }

    /**
     * Form tambah reservasi
     */
    public function create()
    {
        $pelanggan = User::where('role', 'pelanggan')->get();
        $trainer   = User::where('role', 'trainer')->get();
        $kelas     = Kelas::all();

        return view('reservasi.create', compact('pelanggan', 'trainer', 'kelas'));
    }

    /**
     * Simpan reservasi baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:users,id',
            'trainer_id'   => 'required|exists:users,id',
            'kelas_id'     => 'required|exists:kelas,id',
            'jadwal'       => 'required|date',
            'status'       => 'required|in:pending,approved,canceled',
        ]);

        Reservasi::create($request->all());

        return redirect()->route('reservasi.index')->with('success', 'Reservasi berhasil ditambahkan');
    }

    /**
     * Tampilkan detail reservasi
     */
    public function show(Reservasi $reservasi)
    {
        return view('reservasi.show', compact('reservasi'));
    }

    /**
     * Form edit reservasi
     */
    public function edit(Reservasi $reservasi)
    {
        $pelanggan = User::where('role', 'pelanggan')->get();
        $trainer   = User::where('role', 'trainer')->get();
        $kelas     = Kelas::all();

        return view('reservasi.edit', compact('reservasi', 'pelanggan', 'trainer', 'kelas'));
    }

    /**
     * Update reservasi
     */
    public function update(Request $request, Reservasi $reservasi)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:users,id',
            'trainer_id'   => 'required|exists:users,id',
            'kelas_id'     => 'required|exists:kelas,id',
            'jadwal'       => 'required|date',
            'status'       => 'required|in:pending,approved,canceled',
        ]);

        $reservasi->update($request->all());

        return redirect()->route('reservasi.index')->with('success', 'Reservasi berhasil diperbarui');
    }

    /**
     * Hapus reservasi
     */
    public function destroy(Reservasi $reservasi)
    {
        $reservasi->delete();

        return redirect()->route('reservasi.index')->with('success', 'Reservasi berhasil dihapus');
    }
}
