<?php

namespace App\Http\Controllers;

use App\Models\Reservasi;
use App\Models\User;
use App\Models\Kelas;
use App\Models\VisitLog;
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
     * Update status reservasi + catat ke visit log
     */
    public function updateStatus(Request $request, $id)
    {
        $reservasi = Reservasi::with('pelanggan')->findOrFail($id);

        // update status reservasi
        $reservasi->update([
            'status' => $request->status,
        ]);

        // catat ke visit_logs
        VisitLog::create([
            'reservasi_id' => $reservasi->id,
            'user_id'      => $reservasi->pelanggan_id, // simpan id pelanggan
            'status'       => $request->status,
            'catatan'      => $request->catatan ?? 'Status diubah menjadi ' . $request->status,
        ]);

        return redirect()->back()->with('success', 'Status reservasi diperbarui dan dicatat di Visit Log.');
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
     * Simpan reservasi baru + log otomatis
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

        // simpan reservasi
        $reservasi = Reservasi::create($request->all());

        // langsung catat ke visit_logs
        VisitLog::create([
            'reservasi_id' => $reservasi->id,
            'user_id'      => $reservasi->pelanggan_id,
            'status'       => $reservasi->status,
            'catatan'      => 'Reservasi baru dibuat dengan status ' . $reservasi->status,
        ]);

        return redirect()->route('reservasi.index')->with('success', 'Reservasi berhasil ditambahkan dan dicatat di Visit Log');
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
     * Update reservasi (data umum, bukan status)
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
