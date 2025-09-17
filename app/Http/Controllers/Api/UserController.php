<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // List all users
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    // Create new user
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json($user, 201);
    }

    // Show single user
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);
        return response()->json($user);
    }

    // Update user
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes','required','string','email', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|required|string|min:6',
        ]);

        if($request->has('name')) $user->name = $request->name;
        if($request->has('email')) $user->email = $request->email;
        if($request->has('password')) $user->password = Hash::make($request->password);

        $user->save();

        return response()->json($user);
    }

    // Delete user
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
