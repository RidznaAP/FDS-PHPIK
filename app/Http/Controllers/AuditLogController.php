<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        // Hanya Pusat yang bisa melihat halaman audit log
        if (!Auth::user()->isPusat()) {
            abort(403, 'Akses ditolak. Fitur ini khusus untuk Admin Pusat.');
        }

        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $logs = $query->paginate(20)->withQueryString();
        
        $users = \App\Models\User::orderBy('name', 'asc')->get();

        return view('audit.index', compact('logs', 'users'));
    }
}
