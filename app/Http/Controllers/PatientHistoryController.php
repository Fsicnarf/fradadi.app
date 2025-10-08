<?php

namespace App\Http\Controllers;

use App\Models\PatientFile;
use App\Models\Appointment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PatientHistoryController extends Controller
{
    public function show(Request $request, string $key)
    {
        $user = Auth::user();
        $files = PatientFile::with('user')
            ->where('key', $key)
            ->orderByDesc('created_at')
            ->paginate(12);
        $deleted = PatientFile::onlyTrashed()->with('user')
            ->where('key', $key)
            ->orderByDesc('deleted_at')
            ->paginate(10, ['*'], 'trash');
        // Derivar meta de paciente desde citas
        $name = null; $dni = null; $age = null; $lastVisit = null;
        if (str_starts_with($key, 'NAME:')) {
            $name = substr($key, 5);
            $appt = Appointment::query()
                ->where('patient_name', $name)
                ->orderByDesc('start_at')->first();
        } else {
            $dni = $key;
            $appt = Appointment::query()
                ->where('dni', $dni)
                ->orderByDesc('start_at')->first();
            if ($appt) { $name = $appt->patient_name; }
        }
        if ($appt) { $age = $appt->patient_age; $lastVisit = $appt->start_at; }
        return view('user.patient_history', [
            'key' => $key,
            'files' => $files,
            'deleted' => $deleted,
            'p_name' => $name,
            'p_dni' => $dni,
            'p_age' => $age,
            'p_last' => $lastVisit,
        ]);
    }

    public function store(Request $request, string $key)
    {
        $user = Auth::user();
        $data = $request->validate([
            'file' => ['required','file','max:51200'], // 50 MB
            'title' => ['nullable','string','max:150'],
            'description' => ['nullable','string','max:1000'],
        ]);
        $uploaded = $data['file'];
        $path = $uploaded->store('patient_files/'.$user->id, 'public');
        $pf = PatientFile::create([
            'user_id' => $user->id,
            'key' => $key,
            'path' => $path,
            'title' => $data['title'] ?? null,
            'original_name' => $uploaded->getClientOriginalName(),
            'mime' => $uploaded->getClientMimeType(),
            'size' => $uploaded->getSize(),
            'description' => $data['description'] ?? null,
        ]);
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'patient_file_create',
            'description' => 'Archivo creado para '.$key.' (ID '.$pf->id.')',
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        return redirect()->route('patients.history', ['key' => $key])->with('ok', 'Archivo subido');
    }

    public function update(Request $request, string $key, PatientFile $file)
    {
        $user = Auth::user();
        $data = $request->validate([
            'title' => ['nullable','string','max:150'],
            'description' => ['nullable','string','max:1000'],
        ]);
        $file->title = $data['title'] ?? $file->title;
        $file->description = $data['description'] ?? null;
        $file->save();
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'patient_file_update',
            'description' => 'Descripción actualizada para '.$key.' (ID '.$file->id.')',
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        return redirect()->route('patients.history', ['key' => $key])->with('ok','Descripción actualizada');
    }

    public function destroy(Request $request, string $key, PatientFile $file)
    {
        $user = Auth::user();
        if ($file->path) {
            Storage::disk('public')->delete($file->path);
        }
        $file->delete();
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'patient_file_delete',
            'description' => 'Archivo eliminado de '.$key.' (ID '.$file->id.')',
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        return redirect()->route('patients.history', ['key' => $key])->with('ok','Archivo eliminado');
    }

    public function restore(Request $request, string $key, int $file)
    {
        $user = Auth::user();
        $pf = PatientFile::onlyTrashed()->where('id', $file)->where('key', $key)->firstOrFail();
        $pf->restore();
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'patient_file_restore',
            'description' => 'Archivo restaurado de '.$key.' (ID '.$pf->id.')',
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        return redirect()->route('patients.history', ['key' => $key])->with('ok','Archivo restaurado');
    }

    public function bulkDestroy(Request $request, string $key)
    {
        $user = Auth::user();
        $data = $request->validate([
            'ids' => ['required','array'],
            'ids.*' => ['integer'],
        ]);
        $ids = $data['ids'];
        $files = PatientFile::whereIn('id', $ids)->where('key', $key)->get();
        $count = 0;
        foreach ($files as $file) {
            if ($file->path) {
                Storage::disk('public')->delete($file->path);
            }
            $file->delete();
            $count++;
        }
        if ($count > 0) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'patient_file_bulk_delete',
                'description' => "Eliminación masiva ({$count}) de $key",
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }
        return redirect()->route('patients.history', ['key' => $key])->with('ok', "Archivos eliminados: $count");
    }
}
