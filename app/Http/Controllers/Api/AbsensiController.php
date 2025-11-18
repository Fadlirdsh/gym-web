<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Zona;
use App\Models\Absensi;

class AbsensiController extends Controller
{
    // Fungsi Haversine
    private function haversineDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371000; // meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c; // jarak dalam meter
    }

    public function absen(Request $request)
    {
        $request->validate([
            'zona_id' => 'required|exists:tbl_zona,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $zona = Zona::findOrFail($request->zona_id);
        $jarak = $this->haversineDistance($request->latitude, $request->longitude, $zona->latitude, $zona->longitude);

        if ($jarak <= $zona->radius_m) {
            Absensi::create([
                'user_id' => $request->user()->id,
                'zona_id' => $zona->id,
                'waktu' => now(),
            ]);
            return response()->json(['success' => true, 'message' => 'Absensi berhasil']);
        } else {
            return response()->json(['success' => false, 'message' => 'Anda berada di luar zona!']);
        }
    }
}
