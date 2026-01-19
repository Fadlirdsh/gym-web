<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * HISTORY TRANSAKSI (LIST STRUK)
     * GET /api/transaksi
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $transaksis = Transaksi::with(['items:id,transaksi_id,item_name,price,qty'])
            ->select('id', 'kode_transaksi', 'status', 'total_bayar', 'metode')
            ->where('user_id', $user->id)
            ->paginate(10);

        return response()->json([
            'message' => 'History transaksi',
            'data' => $transaksis,
        ]);
    }

    /**
     * DETAIL STRUK TRANSAKSI
     * GET /api/transaksi/{kode_transaksi}
     */
    public function show(Request $request, $kode)
    {
        $user = $request->user();

        $transaksi = Transaksi::with('items')
            ->where('kode_transaksi', $kode)
            ->where('user_id', $user->id)
            ->first();

        if (!$transaksi) {
            return response()->json([
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail transaksi',
            'data' => $transaksi,
        ]);
    }
}
