<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Member;
use App\Models\TokenPackage;

class MemberController extends Controller
{
    // Menampilkan halaman manage member
    public function index()
    {
        // Ambil semua pelanggan (role = 'pelanggan')
        $pelanggan = User::where('role', 'pelanggan')->get();

        // Ambil semua member
        $members = Member::all();

        // Ambil semua token package
        $tokenPackages = TokenPackage::all();

        // ENUM TIPE KELAS (sesuai migration)
        $tipeKelasList = [
            'Pilates Group',
            'Pilates Private',
            'Yoga Group',
            'Yoga Private',
        ];

        return view('admin.manage_member', compact(
            'pelanggan',
            'members',
            'tipeKelasList',
            'tokenPackages'
        ));
    }

    // Membuat member baru manual
    public function store(Request $request)
    {
        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'tipe_kelas'  => 'required|string|max:255',
        ]);

        $user = User::find($request->user_id);

        // Cek apakah user sudah member
        if (Member::where('user_id', $user->id)->exists()) {
            return redirect()->back()->with('error', 'User ini sudah menjadi member.');
        }

        Member::create([
            'user_id'        => $user->id,
            'nama'           => $user->name,
            'email'          => $user->email,
            'tipe_kelas'     => $request->tipe_kelas,
            'harga'          => 0,
            'token_total'    => 0,
            'token_terpakai' => 0,
            'token_sisa'     => 0,
            'status'         => 'aktif',
        ]);

        return redirect()->back()->with('success', 'Member baru berhasil dibuat!');
    }

    // Menambahkan user pelanggan jadi member
    public function assignUser(Request $request)
    {
        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'tipe_kelas'  => 'required|string|max:255',
        ]);

        // Cek apakah sudah member
        if (Member::where('user_id', $request->user_id)->exists()) {
            return redirect()->back()->with('error', 'User ini sudah menjadi member.');
        }

        $user = User::find($request->user_id);

        Member::create([
            'user_id'        => $user->id,
            'nama'           => $user->name,
            'email'          => $user->email,
            'tipe_kelas'     => $request->tipe_kelas,
            'harga'          => 0,
            'token_total'    => 0,
            'token_terpakai' => 0,
            'token_sisa'     => 0,
            'status'         => 'aktif',
        ]);

        return redirect()->back()->with('success', 'User berhasil ditambahkan sebagai member!');
    }
}
