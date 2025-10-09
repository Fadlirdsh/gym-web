<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * GET /api/kelas
     * Ambil semua data kelas dengan relasi jadwal dan trainer.
     */
    public function index()
    {
        $kelas = Kelas::with([
            'schedules' => fn($query) => $query->where('is_active', true),
            'trainer'
        ])->withCount('reservasi')->get();

        $data = $kelas->map(function ($item) {
            $jadwal = $item->schedules->first(); // ambil satu jadwal aktif

            return [
                'id'            => $item->id,
                'nama_kelas'    => $item->nama_kelas,
                'tipe_kelas'    => $item->tipe_kelas,
                'harga'         => $item->harga,
                'deskripsi'     => $item->deskripsi,
                'tipe_paket'    => $item->tipe_paket,
                'jumlah_token'  => $item->jumlah_token,
                'expired_at'    => $item->expired_at,
                'waktu_mulai'   => $item->waktu_mulai,
                'diskon_persen' => $item->diskon_persen,
                'harga_diskon'  => $item->harga_diskon,
                'sisa_kursi'    => $item->sisa_kursi,

                // Info jadwal dan instruktur
                'hari'          => $jadwal->day ?? null,
                'jam_mulai'     => $jadwal->time ?? null,
                'instruktur'    => $item->trainer->name ?? 'Instruktur',

                // Perbaikan gambar supaya URL valid
                'gambar' => $item->gambar ? url($item->gambar) : null,
            ];
        });

        return response()->json($data);
    }

    /**
     * GET /api/kelas/{id}
     * Ambil detail 1 kelas.
     */
    public function show($id)
    {
        $kelas = Kelas::with(['schedules', 'trainer', 'reservasi'])
            ->withCount('reservasi')
            ->findOrFail($id);

        $jadwal = $kelas->schedules->first();

        return response()->json([
            'id'            => $kelas->id,
            'nama_kelas'    => $kelas->nama_kelas,
            'tipe_kelas'    => $kelas->tipe_kelas,
            'harga'         => $kelas->harga,
            'deskripsi'     => $kelas->deskripsi,
            'tipe_paket'    => $kelas->tipe_paket,
            'jumlah_token'  => $kelas->jumlah_token,
            'expired_at'    => $kelas->expired_at,
            'waktu_mulai'   => $kelas->waktu_mulai,
            'diskon_persen' => $kelas->diskon_persen,
            'harga_diskon'  => $kelas->harga_diskon,
            'sisa_kursi'    => $kelas->sisa_kursi,
            'hari'          => $jadwal->day ?? null,
            'jam_mulai'     => $jadwal->time ?? null,
            'instruktur'    => $kelas->trainer->name ?? 'Instruktur',

            // Perbaikan gambar supaya URL valid
            'gambar'        => $kelas->gambar ? asset(implode('/', array_map('urlencode', explode('/', $kelas->gambar)))) : null,
        ]);
    }

    /**
     * PUT /api/kelas/{id}
     */
    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->update($request->only([
            'nama_kelas',
            'tipe_kelas',
            'harga',
            'deskripsi',
            'kapasitas',
            'tipe_paket',
            'jumlah_token',
            'gambar'
        ]));

        return response()->json([
            'message' => 'Kelas berhasil diperbarui',
            'data'    => $kelas,
        ]);
    }

    /**
     * DELETE /api/kelas/{id}
     */
    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();

        return response()->json(['message' => 'Kelas berhasil dihapus']);
    }
}
