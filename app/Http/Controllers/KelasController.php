<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\QrCode;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode as SimpleQrCode;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::where('expired_at', '>=', now())
            ->with('diskons')
            ->withCount('reservasi')
            ->get();

        return view('admin.Kelas', compact('kelas'));
    }

    public function create()
    {
        return view('users.kelas-create');
    }

    public function store(Request $request)
    {
        // Validasi request dsb...

        $kelas = Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'tipe_kelas' => $request->tipe_kelas,
            'harga' => $request->harga,
            'kapasitas' => $request->kapasitas,
            'deskripsi' => $request->deskripsi,
            'expired_at' => $request->expired_at,
        ]);

        // Generate QR sebagai gambar
        $qrImage = SimpleQrCode::format('png')->size(300)
            ->generate(url("/absensi/{$kelas->id}"));

        // Simpan file QR di storage
        $path = "qr/kelas_{$kelas->id}.png";
        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $qrImage);

        // Simpan ke DB
        QrCode::create([
            'kelas_id' => $kelas->id,
            'qr_url' => "/storage/{$path}",
        ]);

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

        // Ganti gambar jika ada upload baru
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

        $kelas->delete();
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus');
    }

    /**
     * API untuk mobile app (Ionic)
     */
    public function apiIndex()
    {
        $kelas = Kelas::where('expired_at', '>=', now())
            ->with('diskons')
            ->withCount('reservasi')
            ->get();

        $data = $kelas->map(function ($item) {
            return [
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
            ];
        });

        return response()->json($data);
    }
    public function getQr($id)
    {
        $qr = QrCode::where('kelas_id', $id)->first();

        if (!$qr) {
            return response()->json(['error' => 'QR tidak ditemukan'], 404);
        }

        return response()->json([
            'kelas' => Kelas::find($id)->nama_kelas,
            'qr_url' => asset($qr->qr_url),
        ]);
    }
}
