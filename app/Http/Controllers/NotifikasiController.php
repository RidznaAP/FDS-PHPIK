<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notifikasi;

class NotifikasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Halaman daftar notifikasi */
    public function index(Request $request)
    {
        $query = Notifikasi::where('user_id', Auth::id())->with('dariUser')->latest();

        // Filter: sudah/belum dibaca
        if ($request->has('dibaca') && $request->dibaca !== null && $request->dibaca !== '') {
            $query->where('dibaca', (int)$request->dibaca);
        }

        // Search: judul atau pesan
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('judul', 'like', "%{$q}%")
                   ->orWhere('pesan', 'like', "%{$q}%");
            });
        }

        $notifikasis = $query->paginate(20)->withQueryString();

        // Tandai semua sebagai dibaca hanya jika masuk ke halaman utama tanpa filter apapun
        if (!$request->has('dibaca') && !$request->filled('search')) {
            Notifikasi::where('user_id', Auth::id())
                ->where('dibaca', false)
                ->update(['dibaca' => true]);
        }

        return view('notifikasi.index', compact('notifikasis'));
    }

    /** Tandai satu notifikasi sebagai dibaca & redirect ke url tujuan */
    public function baca($id)
    {
        $notif = Notifikasi::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notif->update(['dibaca' => true]);

        return $notif->url
            ? redirect($notif->url)
            : redirect()->route('notifikasi.index');
    }

    /** Tandai semua sebagai dibaca (AJAX) */
    public function bacaSemua()
    {
        Notifikasi::where('user_id', Auth::id())
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        return response()->json(['ok' => true]);
    }

    /** Ambil jumlah notifikasi belum dibaca (AJAX polling) */
    public function jumlah()
    {
        $count = Notifikasi::where('user_id', Auth::id())
            ->where('dibaca', false)
            ->count();

        $latest = Notifikasi::where('user_id', Auth::id())
            ->with('dariUser')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($n) => [
                'id'        => $n->id,
                'judul'     => $n->judul,
                'pesan'     => $n->pesan,
                'tipe'      => $n->tipe,
                'url'       => $n->url,
                'dibaca'    => $n->dibaca,
                'dari'      => $n->dariUser?->name ?? 'Sistem',
                'waktu'     => $n->created_at->diffForHumans(),
            ]);

        return response()->json(['count' => $count, 'latest' => $latest]);
    }

    /** Hapus semua notifikasi milik user */
    public function hapusSemua()
    {
        Notifikasi::where('user_id', Auth::id())->delete();
        return back()->with('success', 'Semua notifikasi telah dibersihkan.');
    }
}
