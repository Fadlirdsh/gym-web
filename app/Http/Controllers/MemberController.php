<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Member;

class MemberController extends Controller
{
    /**
     * ================================
     * HALAMAN MANAGE MEMBER (ADMIN)
     * ================================
     */
    public function index()
    {
        // HANYA pelanggan yang BELUM punya member aktif
        $pelanggan = User::where('role', 'pelanggan')
            ->whereDoesntHave('member', function ($q) {
                $q->where('status', 'aktif');
            })
            ->get();

        // Semua member (pending, aktif, nonaktif)
        $members = Member::with('user')->latest()->get();

        return view('admin.manage_member', compact(
            'pelanggan',
            'members'
        ));
    }

    /**
     * ================================
     * BUAT MEMBER BARU (ADMIN)
     * ================================
     * STATUS AWAL: PENDING
     * (ANTI HUMAN ERROR)
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);

        // Cegah double member (SEMUA STATUS)
        if (Member::where('user_id', $user->id)->exists()) {
            return redirect()->back()
                ->with('error', 'User ini sudah memiliki data membership.');
        }

        Member::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'tanggal_mulai' => null,
            'tanggal_berakhir' => null,
            'activated_by_transaction_id' => null,
        ]);

        return redirect()->back()
            ->with('success', 'Member berhasil dibuat dengan status PENDING.');
    }

    /**
     * ======================================
     * AKTIVASI MEMBER (ADMIN)
     * ======================================
     * INI SATU-SATUNYA TEMPAT AKTIF
     */
    public function activate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $member = Member::where('user_id', $request->user_id)->first();

        if (!$member) {
            return redirect()->back()
                ->with('error', 'Data member tidak ditemukan.');
        }

        if ($member->status === 'aktif') {
            return redirect()->back()
                ->with('error', 'Membership sudah aktif.');
        }

        $member->update([
            'status' => 'aktif',
            'tanggal_mulai' => now(),
            'tanggal_berakhir' => now()->addMonth(),
            'activated_by_transaction_id' => null, // ADMIN MANUAL
        ]);

        return redirect()->back()
            ->with('success', 'Membership berhasil diaktifkan.');
    }

    /**
     * ================================
     * NONAKTIFKAN MEMBER (ADMIN)
     * ================================
     */
    public function deactivate($id)
    {
        $member = Member::findOrFail($id);

        $member->update([
            'status' => 'nonaktif',
            'tanggal_berakhir' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Membership berhasil dinonaktifkan.');
    }
}
