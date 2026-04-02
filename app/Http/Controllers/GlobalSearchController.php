<?php

namespace App\Http\Controllers;

use App\Models\Perencanaan;
use App\Models\Pelaksanaan;
use App\Models\Laboratorium;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->input('q');
        
        if (empty($q)) {
            return back()->with('error', 'Masukkan kata kunci pencarian.');
        }

        $user = Auth::user();

        // Cari di Perencanaan
        $perencanaanQuery = Perencanaan::where(function($query) use ($q) {
            $query->where('kab_kota', 'like', "%{$q}%")
                  ->orWhere('provinsi', 'like', "%{$q}%")
                  ->orWhere('jenis_mp', 'like', "%{$q}%")
                  ->orWhere('jenis_hpik', 'like', "%{$q}%");
        });

        // Cari di Pelaksanaan
        $pelaksanaanQuery = Pelaksanaan::with(['perencanaan', 'laboratorium'])->where(function($query) use ($q) {
            $query->where('lokasi_pengambilan_sampel', 'like', "%{$q}%")
                  ->orWhere('jenis_ikan', 'like', "%{$q}%")
                  ->orWhere('nama_latin', 'like', "%{$q}%")
                  ->orWhereHas('perencanaan', function($q2) use ($q) {
                      $q2->where('kab_kota', 'like', "%{$q}%")->orWhere('provinsi', 'like', "%{$q}%");
                  });
        });

        // Cari di Laboratorium
        $laboratoriumQuery = Laboratorium::with(['pelaksanaan.perencanaan'])->where(function($query) use ($q) {
            $query->where('metode_uji', 'like', "%{$q}%")
                  ->orWhere('hasil_uji', 'like', "%{$q}%")
                  ->orWhereHas('pelaksanaan', function($q2) use ($q) {
                      $q2->where('lokasi_pengambilan_sampel', 'like', "%{$q}%")
                         ->orWhere('jenis_ikan', 'like', "%{$q}%");
                  });
        });

        // Aplikasikan Role Constraints
        if ($user->isBkhit()) {
            $perencanaanQuery->where('user_id', $user->id);
            $pelaksanaanQuery->whereHas('perencanaan', function($query) use ($user) {
                $query->where('user_id', $user->id);
            });
            $laboratoriumQuery->whereHas('pelaksanaan.perencanaan', function($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        } elseif ($user->isBbkhit()) {
            $perencanaanQuery->where(function($qBuilder) use ($user) {
                $qBuilder->where('user_id', $user->id)
                         ->orWhere('status', '!=', 'draft');
            });
            $pelaksanaanQuery->whereHas('perencanaan', function($query) use ($user) {
                $query->where('user_id', $user->id)->orWhere('status', '!=', 'draft');
            });
            $laboratoriumQuery->whereHas('pelaksanaan.perencanaan', function($query) use ($user) {
                $query->where('user_id', $user->id)->orWhere('status', '!=', 'draft');
            });
        }

        $perencanaans = $perencanaanQuery->take(10)->get();
        $pelaksanaans = $pelaksanaanQuery->take(10)->get();
        $laboratoriums = $laboratoriumQuery->take(10)->get();

        return view('search.index', compact('q', 'perencanaans', 'pelaksanaans', 'laboratoriums'));
    }
}
