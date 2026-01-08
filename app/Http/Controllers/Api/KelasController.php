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
            'schedules.trainerShift.trainer'
        ])
        ->where(function ($q) {
            $q->whereNull('expired_at')
              ->orWhere('expired_at', '>=', now());
        })
        ->get();

        $data = $kelas->map(function ($k) {
            $schedule = $k->schedules->first();

            return [
                'id'         => $k->id,
                'nama_kelas' => $k->nama_kelas,
                'tipe_kelas' => $k->tipe_kelas,
                'harga'      => $k->harga,
                'deskripsi'  => $k->deskripsi,
                'gambar'     => $k->gambar ? url($k->gambar) : null,
                'expired_at' => $k->expired_at,

                'hari'       => $schedule?->trainerShift?->day,
                'jam_mulai'  => $schedule?->start_time,
                'instruktur' => $schedule?->trainerShift?->trainer?->name,
                'sisa_kursi' => $schedule
                    ? $schedule->sisaSlot(now()->toDateString())
                    : null,
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
            'expired_at'    => $kelas->expired_at, // ✅ TAMBAH INI
            'waktu_mulai'   => $kelas->waktu_mulai,
            'diskon_persen' => $kelas->diskon_persen,
            'harga_diskon'  => $kelas->harga_diskon,
            'sisa_kursi'    => $kelas->sisa_kursi,
            'tipe_paket'    => $kelas->tipe_paket,
            'hari'          => $jadwal->day ?? null,
            'jam_mulai'     => $jadwal->time ?? null,
            'instruktur'    => $kelas->trainer->name ?? 'Instruktur',
            'gambar'        => $kelas->gambar
                ? asset(implode('/', array_map('urlencode', explode('/', $kelas->gambar))))
                : null,
        ]);
    }

    /**
     * POST /api/kelas
     * Tambah data kelas baru.
     * tipe_paket otomatis di-set ke "General".
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'tipe_kelas' => 'required|string|max:50',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'kapasitas' => 'required|integer|min:1',
            'expired_at' => 'nullable|date', // ✅ VALIDASI DITAMBAH
            'gambar' => 'nullable|image|max:2048',
        ]);

        // Upload gambar jika ada
        $gambarPath = null;

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');

            $filename = time() . '_' . $file->getClientOriginalName();

            $file->move(public_path('uploads/kelas'), $filename);

            // SIMPAN RELATIVE PATH KE DB
            $gambarPath = 'uploads/kelas/' . $filename;
        }

        $kelas = Kelas::create([
            'nama_kelas' => $validated['nama_kelas'],
            'tipe_kelas' => $validated['tipe_kelas'],
            'harga' => $validated['harga'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'kapasitas' => $validated['kapasitas'],
            'tipe_paket' => 'General',
            'jumlah_token' => null,
            'expired_at' => $validated['expired_at'] ?? null, // ✅ SIMPAN EXPIRED
            'gambar' => $gambarPath,
        ]);

        return response()->json([
            'message' => 'Kelas berhasil dibuat',
            'data'    => $kelas,
        ], 201);
    }

    /**
     * PUT /api/kelas/{id}
     * Update data kelas.
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
            'gambar',
            'expired_at', // ✅ IZINKAN UPDATE EXPIRED
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
