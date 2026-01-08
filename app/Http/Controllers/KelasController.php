<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::where(function ($q) {
                $q->whereNull('expired_at')
                  ->orWhere('expired_at', '>=', now());
            })
            ->with('diskons')
            ->get();

        return view('admin.Kelas', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'tipe_kelas' => 'required|string|max:50',
            'harga'      => 'required|numeric',
            'deskripsi'  => 'nullable|string',
            'expired_at' => 'nullable|date',
            'gambar'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(
            'nama_kelas',
            'tipe_kelas',
            'harga',
            'deskripsi',
            'expired_at'
        );

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $namaFile = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/kelas'), $namaFile);
            $data['gambar'] = 'uploads/kelas/' . $namaFile;
        }

        Kelas::create($data);

        return redirect()
            ->route('kelas.index')
            ->with('success', 'Kelas berhasil dibuat!');
    }

    public function update(Request $request, Kelas $kelas)
    {
        $validatedData = $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'tipe_kelas' => 'required|string|max:50',
            'harga'      => 'required|numeric',
            'deskripsi'  => 'nullable|string',
            'expired_at' => 'nullable|date',
            'gambar'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            if ($kelas->gambar && file_exists(public_path($kelas->gambar))) {
                unlink(public_path($kelas->gambar));
            }

            $file = $request->file('gambar');
            $namaFile = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/kelas'), $namaFile);
            $validatedData['gambar'] = 'uploads/kelas/' . $namaFile;
        }

        $kelas->update($validatedData);

        return redirect()
            ->route('kelas.index')
            ->with('success', 'Kelas berhasil diperbarui');
    }

    public function destroy(Kelas $kelas)
    {
        if ($kelas->gambar && file_exists(public_path($kelas->gambar))) {
            unlink(public_path($kelas->gambar));
        }

        $kelas->delete();

        return redirect()
            ->route('kelas.index')
            ->with('success', 'Kelas berhasil dihapus');
    }

    /**
     * API — list kelas (mobile)
     * NOTE: TANPA sisa_kursi
     */
    public function apiIndex()
    {
        $kelas = Kelas::where(function ($q) {
                $q->whereNull('expired_at')
                  ->orWhere('expired_at', '>=', now());
            })
            ->with('diskons')
            ->get();

        $data = $kelas->map(fn ($item) => [
            'id'            => $item->id,
            'nama_kelas'    => $item->nama_kelas,
            'tipe_kelas'    => $item->tipe_kelas,
            'harga'         => $item->harga,
            'deskripsi'     => $item->deskripsi,
            'expired_at'    => $item->expired_at,
            'gambar'        => $item->gambar ? asset($item->gambar) : null,
            'diskon_persen' => $item->diskon_persen,
            'harga_diskon'  => $item->harga_diskon,
        ]);

        return response()->json($data);
    }
}
