<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TokenPackage;

class TokenPackageController extends Controller
{
    // Lihat semua paket token
    public function index()
    {
        $packages = TokenPackage::all();
        return view('admin.manage_member', compact('packages'));
    }

    // Tambah paket token baru
    public function store(Request $request)
    {
        $data = $request->validate([
            'jumlah_token' => 'required|integer',
            'tipe_kelas' => 'required|string',
            'harga' => 'required|integer',
        ]);

        TokenPackage::create($data);

        return redirect()->back()->with('success', 'Paket token berhasil ditambahkan!');
    }

    // Form edit paket token
    public function edit($id)
    {
        $package = TokenPackage::findOrFail($id);
        return view('admin.edit_token_package', compact('package'));
    }

    // Update paket token
    public function update(Request $request, $id)
    {
        $package = TokenPackage::findOrFail($id);

        $data = $request->validate([
            'jumlah_token' => 'required|integer',
            'tipe_kelas' => 'required|string',
            'harga' => 'required|integer',
        ]);

        $package->update($data);

        return redirect()->back()->with('success', 'Paket token berhasil diupdate!');
    }

    // Hapus paket token
    public function destroy($id)
    {
        $package = TokenPackage::findOrFail($id);
        $package->delete();

        return redirect()->back()->with('success', 'Paket token berhasil dihapus!');
    }
}
