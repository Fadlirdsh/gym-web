<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // =========================================================
    // 🔹 API Methods (biarkan utuh)
    // =========================================================

    public function index()
    {
        return response()->json(User::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:pelanggan,trainer',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return response()->json($user, 201);
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);

        $request->validate([
            'name'     => 'sometimes|required|string|max:255',
            'email'    => ['sometimes', 'required', 'string', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|required|string|min:6',
        ]);

        if ($request->has('name'))     $user->name = $request->name;
        if ($request->has('email'))    $user->email = $request->email;
        if ($request->has('password')) $user->password = Hash::make($request->password);

        $user->save();

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    // =========================================================
    // 🔸 WEB CRUD: Manage User + Member
    // =========================================================

    /**
     * Menampilkan daftar pelanggan dan member
     */
    public function manage()
    {
        $pelanggan = User::where('role', 'pelanggan')->orderBy('created_at', 'desc')->get();
        $members = User::orderBy('created_at', 'desc')->get();
        return view('admin.manage', compact('pelanggan', 'members'));
    }

    /**
     * Admin membuat akun (hanya pelanggan yang boleh dibuat lewat form web)
     */
    public function storeWeb(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:pelanggan,trainer',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('users.manage')->with('success', 'Akun ' . $request->role . ' berhasil ditambahkan');
    }

    /**
     * Admin mengedit data pelanggan
     */
    public function edit($id)
    {
        $user = User::with('member')->findOrFail($id);
        return view('admin.edit', compact('user'));
    }

    /**
     * Admin memperbarui data pelanggan
     */
    public function updateWeb(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'role'     => 'required|in:pelanggan,trainer',
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->role  = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.manage')->with('success', 'Akun ' . ucfirst($request->role) . ' berhasil diperbarui');
    }

    /**
     * Admin menghapus akun pelanggan
     */
    public function destroyWeb($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.manage')->with('success', 'Akun pelanggan berhasil dihapus');
    }

    /**
     * Admin memberikan / membuat membership untuk pelanggan
     */
    public function beriMember(Request $request, $user_id)
    {
        $request->validate([
            'maks_kelas' => 'required|in:3,5,10,20',
            'tipe_kelas' => 'required|in:Pilates Group,Pilates Private,Yoga Group,Yoga Private',
            'harga'      => 'required|integer|min:0',
        ]);

        $user = User::findOrFail($user_id);

        if ($user->role !== 'pelanggan') {
            return back()->with('error', 'Hanya pelanggan yang bisa dijadikan member.');
        }

        Member::where('user_id', $user->id)->delete();

        Member::create([
            'user_id'         => $user->id,
            'maks_kelas'      => $request->maks_kelas,
            'tipe_kelas'      => $request->tipe_kelas,
            'harga'           => $request->harga,
            'tanggal_mulai'   => now(),
            'tanggal_berakhir' => now()->addMonth(),
            'status'          => 'aktif',
        ]);

        return back()->with('success', 'Membership berhasil dibuat untuk pelanggan.');
    }

    /**
     * API tambahan untuk mengambil semua pelanggan
     */
    public function pelanggan()
    {
        $pelanggan = User::where('role', 'pelanggan')->get();
        return response()->json($pelanggan);
    }
    
}
