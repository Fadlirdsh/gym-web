<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MemberToken;
use Tymon\JWTAuth\Facades\JWTAuth;

class MemberTokenController extends Controller
{
    /**
     * Ambil semua token milik user login
     */
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $member = $user->member;

        if (!$member) {
            return response()->json([]);
        }

        return MemberToken::where('member_id', $member->id)->get();
    }
}
