<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function pending()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403);
        }
        $pending = User::where('role', 'user')->where('approved', false)->orderBy('created_at','asc')->get();
        // Connected users: sessions joined
        $sessions = DB::table('sessions')
            ->select('user_id','ip_address','user_agent','last_activity')
            ->whereNotNull('user_id')
            ->get();
        $connected = User::whereIn('id', $sessions->pluck('user_id'))
            ->get()
            ->keyBy('id');
        $users = User::where('role','user')->orderBy('username')->get();
        return view('admin.pending', compact('pending','sessions','connected','users'));
    }

    public function killSessions(Request $request, User $user)
    {
        $admin = Auth::user();
        if (!$admin || $admin->role !== 'admin') abort(403);
        if ($user->role === 'admin') {
            return back()->withErrors(['general' => 'No se pueden cerrar las sesiones del administrador.']);
        }
        DB::table('sessions')->where('user_id', $user->id)->delete();
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'kill_sessions',
            'description' => 'Sesiones terminadas por '.$admin->username,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        return back()->with('status', 'Sesiones del usuario eliminadas');
    }

    public function toggleActive(Request $request, User $user)
    {
        $admin = Auth::user();
        if (!$admin || $admin->role !== 'admin') abort(403);
        if ($user->role === 'admin') {
            return back()->withErrors(['general' => 'No se puede desactivar la cuenta del administrador.']);
        }
        $user->active = !$user->active;
        $user->save();
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => $user->active ? 'reactivate' : 'deactivate',
            'description' => 'Cambio de estado de cuenta por administrador: '.$admin->username,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        return redirect()->route('admin.pending')->with('status', 'Estado de cuenta cambiado correctamente');
    }

    public function history(User $user, Request $request)
    {
        $admin = Auth::user();
        if (!$admin || $admin->role !== 'admin') abort(403);
        $query = ActivityLog::where('user_id', $user->id);
        if ($action = $request->query('action')) {
            $query->where('action', $action);
        }
        if ($from = $request->query('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('created_at', '<=', $to);
        }
        if ($q = $request->query('q')) {
            $query->where(function($w) use ($q) {
                $w->where('description', 'like', "%$q%")
                  ->orWhere('action', 'like', "%$q%")
                  ->orWhere('ip', 'like', "%$q%")
                  ->orWhere('user_agent', 'like', "%$q%");
            });
        }
        $logs = $query->latest()->paginate(20)->appends($request->query());
        $actions = ActivityLog::where('user_id', $user->id)->select('action')->distinct()->pluck('action');
        return view('admin.history', compact('user','logs','actions'));
    }

    public function exportHistory(Request $request, User $user): StreamedResponse
    {
        $admin = Auth::user();
        if (!$admin || $admin->role !== 'admin') {
            abort(403);
        }
        $query = ActivityLog::where('user_id', $user->id);
        if ($action = $request->query('action')) {
            $query->where('action', $action);
        }
        if ($from = $request->query('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('created_at', '<=', $to);
        }
        $filename = 'historial_'.$user->username.'_'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        return response()->stream(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Fecha','Hora','Accion','IP','User-Agent','Descripcion']);
            $query->orderBy('created_at','desc')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $row) {
                    fputcsv($out, [
                        $row->created_at->format('d/m/Y'),
                        $row->created_at->format('H:i'),
                        $row->action,
                        $row->ip,
                        $row->user_agent,
                        $row->description,
                    ]);
                }
            });
            fclose($out);
        }, 200, $headers);
    }

    public function historyAll(Request $request)
    {
        $admin = Auth::user();
        if (!$admin || $admin->role !== 'admin') {
            abort(403);
        }
        $query = ActivityLog::query()->with('user');
        if ($userId = $request->query('user_id')) {
            $query->where('user_id', $userId);
        }
        if ($action = $request->query('action')) {
            $query->where('action', $action);
        }
        if ($from = $request->query('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('created_at', '<=', $to);
        }
        if ($q = $request->query('q')) {
            $query->where(function($w) use ($q) {
                $w->where('description', 'like', "%$q%")
                  ->orWhere('action', 'like', "%$q%")
                  ->orWhere('ip', 'like', "%$q%")
                  ->orWhere('user_agent', 'like', "%$q%");
            });
        }
        $logs = $query->latest()->paginate(20)->appends($request->query());
        $actions = ActivityLog::select('action')->distinct()->pluck('action');
        $users = User::orderBy('username')->get(['id','username','name']);
        return view('admin.history_all', compact('logs','actions','users'));
    }

    public function exportHistoryAll(Request $request): StreamedResponse
    {
        $admin = Auth::user();
        if (!$admin || $admin->role !== 'admin') {
            abort(403);
        }
        $query = ActivityLog::query()->with('user');
        if ($userId = $request->query('user_id')) {
            $query->where('user_id', $userId);
        }
        if ($action = $request->query('action')) {
            $query->where('action', $action);
        }
        if ($from = $request->query('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('created_at', '<=', $to);
        }
        if ($q = $request->query('q')) {
            $query->where(function($w) use ($q) {
                $w->where('description', 'like', "%$q%")
                  ->orWhere('action', 'like', "%$q%")
                  ->orWhere('ip', 'like', "%$q%")
                  ->orWhere('user_agent', 'like', "%$q%");
            });
        }
        $filename = 'historial_general_'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        return response()->stream(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Fecha','Hora','Usuario','Accion','IP','User-Agent','Descripcion']);
            $query->orderBy('created_at','desc')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $row) {
                    fputcsv($out, [
                        $row->created_at->format('d/m/Y'),
                        $row->created_at->format('H:i'),
                        optional($row->user)->username,
                        $row->action,
                        $row->ip,
                        $row->user_agent,
                        $row->description,
                    ]);
                }
            });
            fclose($out);
        }, 200, $headers);
    }
}
