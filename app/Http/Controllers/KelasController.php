<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        // Ambil semua kelas sekaligus relasi diskon dan hitung jumlah reservasi
        $kelas = Kelas::with('diskons')->withCount('reservasi')->get();

        return view('admin.Kelas', compact('kelas'));
    }

    public function create()
    {
        return view('users.kelas-create');
    }

    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'nama_kelas'   => 'required|string|max:100',
            'tipe_kelas'   => 'required|string|max:50',
            'harga'        => 'required|numeric',
            'deskripsi'    => 'nullable|string',
            'tipe_paket'   => 'nullable|string|max:50',
            'jumlah_token' => strtolower($request->tipe_paket) === 'classes'
                ? 'required|integer|min:1'
                : 'nullable',
            'expired_at'   => strtolower($request->tipe_paket) === 'classes'
                ? 'required|date'
                : 'nullable',
            'kapasitas'    => 'required|integer|min:1',
            'gambar'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Upload gambar jika ada
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $namaFile = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/kelas'), $namaFile);
            $validatedData['gambar'] = 'uploads/kelas/' . $namaFile; // simpan path gambar ke database
        }

        // Simpan data kelas
        Kelas::create($validatedData);

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan');
    }

    public function update(Request $request, Kelas $kelas)
    {
        // Validasi input
        $validatedData = $request->validate([
            'nama_kelas'   => 'required|string|max:100',
            'tipe_kelas'   => 'required|string|max:50',
            'harga'        => 'required|numeric',
            'deskripsi'    => 'nullable|string',
            'tipe_paket'   => 'nullable|string|max:50',
            'jumlah_token' => strtolower($request->tipe_paket) === 'classes'
                ? 'required|integer|min:1'
                : 'nullable',
            'expired_at'   => strtolower($request->tipe_paket) === 'classes'
                ? 'required|date'
                : 'nullable',
            'kapasitas'    => 'required|integer|min:1',
            'gambar'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // ✅ Jika ada gambar baru diupload
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada dan masih tersimpan di folder
            if ($kelas->gambar && file_exists(public_path($kelas->gambar))) {
                unlink(public_path($kelas->gambar));
            }

            // Simpan gambar baru
            $file = $request->file('gambar');
            $namaFile = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/kelas'), $namaFile);
            $validatedData['gambar'] = 'uploads/kelas/' . $namaFile;
        } else {
            // Kalau tidak upload gambar baru, pastikan gambar lama tidak dihapus
            $validatedData['gambar'] = $kelas->gambar;
        }

        // Update data kelas
        $kelas->update($validatedData);

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil diperbarui');
    }

    public function destroy(Kelas $kelas)
    {
        // Hapus gambar jika ada
        if ($kelas->gambar && file_exists(public_path($kelas->gambar))) {
            unlink(public_path($kelas->gambar));
        }

        $kelas->delete();
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus');
    }

    /**
     * API untuk mobile app (Ionic)
     */
    public function apiIndex()
    {
        $kelas = Kelas::with('diskons')->withCount('reservasi')->get();

        $data = $kelas->map(function ($item) {
            return [
                'id'             => $item->id,
                'nama_kelas'     => $item->nama_kelas,
                'tipe_kelas'     => $item->tipe_kelas,
                'harga'          => $item->harga,
                'deskripsi'      => $item->deskripsi,
                'tipe_paket'     => $item->tipe_paket,
                'jumlah_token'   => $item->jumlah_token,
                'expired_at'     => $item->expired_at,
                'waktu_mulai'    => $item->waktu_mulai,
                'gambar'         => $item->gambar ? asset($item->gambar) : null, // ✅ tambahkan gambar ke API
                'diskon_persen'  => $item->diskon_persen,
                'harga_diskon'   => $item->harga_diskon,
                'sisa_kursi'     => $item->sisa_kursi,
            ];
        });

        return response()->json($data);
    }
}
