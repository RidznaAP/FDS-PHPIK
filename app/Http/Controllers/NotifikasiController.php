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
    public function index()
    {
        $notifikasis = Notifikasi::where('user_id', Auth::id())
            ->with('dariUser')
            ->latest()
            ->paginate(20);

        // Tandai semua sebagai dibaca saat membuka halaman
        Notifikasi::where('user_id', Auth::id())
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

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

    /** Hapus satu notifikasi */
    public function hapus($id)
    {
        Notifikasi::where('id', $id)->where('user_id', Auth::id())->delete();
        return back()->with('success', 'Notifikasi dihapus.');
    }
}
