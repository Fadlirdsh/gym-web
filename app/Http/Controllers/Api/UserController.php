<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kelas; // <-- penting
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // =========================================================
    // API Methods (biarkan utuh)
    // =========================================================

    public function index()
    {
        return response()->json(User::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
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
    // WEB CRUD untuk Manage Member
    // =========================================================

    public function manage()
    {
        $members = User::where('role', 'pelanggan')->get();
        $kelas   = Kelas::all(); // <== kirim daftar kelas ke view
        return view('admin.manage', compact('members', 'kelas'));
    }

    public function storeWeb(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:6|confirmed',
            'kelas_id'              => 'required|exists:kelas,id',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'pelanggan',
            'kelas_id' => $request->kelas_id,
        ]);

        return redirect()->route('users.manage')->with('success', 'Member berhasil ditambahkan');
    }

    public function edit($id)
    {
        $member = User::findOrFail($id);
        $kelas  = Kelas::all();
        return view('admin.edit', compact('member', 'kelas'));
    }

    public function updateWeb(Request $request, $id)
    {
        $member = User::findOrFail($id);

        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => ['required', 'email', Rule::unique('users')->ignore($member->id)],
            'password'              => 'nullable|string|min:6|confirmed',
            'kelas_id'              => 'required|exists:kelas,id',
        ]);

        $member->name     = $request->name;
        $member->email    = $request->email;
        $member->kelas_id = $request->kelas_id;
        if ($request->filled('password')) {
            $member->password = Hash::make($request->password);
        }
        $member->save();

        return redirect()->route('users.manage')->with('success', 'Member berhasil diperbarui');
    }

    public function destroyWeb($id)
    {
        $member = User::findOrFail($id);
        $member->delete();

        return redirect()->route('users.manage')->with('success', 'Member berhasil dihapus');
    }

    public function pelanggan()
    {
        $pelanggan = User::where('role', 'pelanggan')->get();
        return response()->json($pelanggan);
    }
}
