<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\QrCode;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode as SimpleQrCode;
use Illuminate\Support\Facades\Storage;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::where('expired_at', '>=', now())
            ->with('diskons')
            ->withCount('reservasi')
            ->with('qr') // ambil QR juga
            ->get();

        return view('admin.Kelas', compact('kelas'));
    }

    public function create()
    {
        return view('users.kelas-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'tipe_kelas' => 'required|string|max:50',
            'harga'      => 'required|numeric',
            'kapasitas'  => 'required|integer|min:1',
            'deskripsi'  => 'nullable|string',
            'expired_at' => 'nullable|date',
            'gambar'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // validasi gambar
        ]);

        $data = $request->only('nama_kelas', 'tipe_kelas', 'harga', 'kapasitas', 'deskripsi', 'expired_at');

        // Simpan gambar kalau ada
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $namaFile = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/kelas'), $namaFile);
            $data['gambar'] = 'uploads/kelas/' . $namaFile;
        }

        $kelas = Kelas::create($data);

        // Generate QR
        try {
            $qrImage = SimpleQrCode::format('png')
                ->size(300)
                ->errorCorrection('H')
                ->backend('gd')
                ->generate(url("/absensi/{$kelas->id}"));

            $path = "qr/kelas_{$kelas->id}.png";

            if (!Storage::disk('public')->exists('qr')) {
                Storage::disk('public')->makeDirectory('qr');
            }

            Storage::disk('public')->put($path, $qrImage);

            QrCode::create([
                'kelas_id' => $kelas->id,
                'qr_url'   => "/storage/{$path}",
            ]);
        } catch (\Exception $e) {
            return redirect()->route('kelas.index')->with('warning', 'Kelas dibuat, tapi gagal membuat QR: ' . $e->getMessage());
        }

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dibuat!');
    }

    public function update(Request $request, Kelas $kelas)
    {
        $validatedData = $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'tipe_kelas' => 'required|string|max:50',
            'harga'      => 'required|numeric',
            'deskripsi'  => 'nullable|string',
            'expired_at' => 'nullable|date',
            'kapasitas'  => 'required|integer|min:1',
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
        } else {
            $validatedData['gambar'] = $kelas->gambar;
        }

        $kelas->update($validatedData);
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil diperbarui');
    }

    public function destroy(Kelas $kelas)
    {
        if ($kelas->gambar && file_exists(public_path($kelas->gambar))) {
            unlink(public_path($kelas->gambar));
        }

        if ($kelas->qr && file_exists(public_path($kelas->qr->qr_url))) {
            unlink(public_path($kelas->qr->qr_url));
            $kelas->qr()->delete();
        }

        $kelas->delete();
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus');
    }

    public function apiIndex()
    {
        $kelas = Kelas::where('expired_at', '>=', now())
            ->with('diskons')
            ->withCount('reservasi')
            ->with('qr')
            ->get();

        $data = $kelas->map(fn($item) => [
            'id'            => $item->id,
            'nama_kelas'    => $item->nama_kelas,
            'tipe_kelas'    => $item->tipe_kelas,
            'harga'         => $item->harga,
            'deskripsi'     => $item->deskripsi,
            'expired_at'    => $item->expired_at,
            'gambar'        => $item->gambar ? asset($item->gambar) : null,
            'diskon_persen' => $item->diskon_persen,
            'harga_diskon'  => $item->harga_diskon,
            'sisa_kursi'    => $item->sisa_kursi,
            'qr_url'        => $item->qr ? asset($item->qr->qr_url) : null,
        ]);

        return response()->json($data);
    }

    public function getQr($id)
    {
        $qr = QrCode::where('kelas_id', $id)->first();
        if (!$qr) return response()->json(['error' => 'QR tidak ditemukan'], 404);

        return response()->json([
            'kelas' => $qr->kelas->nama_kelas,
            'qr_url' => asset($qr->qr_url),
        ]);
    }
}
