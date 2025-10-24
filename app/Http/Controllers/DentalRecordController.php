<?php

namespace App\Http\Controllers;

use App\Models\DentalRecord;
use App\Models\Appointment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DentalRecordController extends Controller
{
    public function show(Request $request, string $key)
    {
        $user = Auth::user();
        $record = DentalRecord::where('user_id', $user->id)->where('key', $key)->first();
        // Derivar meta de paciente desde citas
        $name = null; $dni = null; $age = null; $lastVisit = null;
        if (str_starts_with($key, 'NAME:')) {
            $name = substr($key, 5);
            $appt = Appointment::query()->where('patient_name', $name)->orderByDesc('start_at')->first();
        } else {
            $dni = $key;
            $appt = Appointment::query()->where('dni', $dni)->orderByDesc('start_at')->first();
            if ($appt) { $name = $appt->patient_name; }
        }
        if ($appt) { $age = $appt->patient_age; $lastVisit = $appt->start_at; }
        return view('user.dental_record', [
            'key' => $key,
            'record' => $record,
            'p_name' => $name,
            'p_dni' => $dni,
            'p_age' => $age,
            'p_last' => $lastVisit,
        ]);
    }

    public function save(Request $request, string $key)
    {
        $user = Auth::user();
        $data = $request->validate([
            'data' => ['nullable','string'], // JSON en string
            'note' => ['nullable','string','max:2000'],
        ]);
        $payload = [];
        if (!empty($data['data'])) {
            $decoded = json_decode($data['data'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $payload = $decoded;
            }
        }
        if (!empty($data['note'])) {
            $payload['note'] = $data['note'];
        }
        $record = DentalRecord::updateOrCreate(
            ['user_id' => $user->id, 'key' => $key],
            ['data' => $payload]
        );
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'dental_record_save',
            'description' => 'Ficha odontológica guardada para '.$key.' (ID '.$record->id.')',
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        return redirect()->route('patients.dental.show', ['key' => $key])->with('ok', 'Ficha odontológica guardada');
    }
}
