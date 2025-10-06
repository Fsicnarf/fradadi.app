<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        // For now, redirect to dashboard where the calendar lives
        return redirect()->route('dashboard');
    }

    public function events(Request $request)
    {
        $user = Auth::user();
        $events = Appointment::where('user_id', $user->id)
            ->orderBy('start_at', 'asc')
            ->get()
            ->map(function ($a) {
                return [
                    'id' => $a->id,
                    'title' => $a->title ?: 'Cita',
                    'start' => $a->start_at->toIso8601String(),
                    'end' => optional($a->end_at)->toIso8601String(),
                    'appointment_type' => $a->appointment_type,
                    'channel' => $a->channel,
                    'notes' => $a->notes,
                    'dni' => $a->dni,
                    'patient_name' => $a->patient_name,
                    'patient_age' => $a->patient_age,
                    'phone' => $a->phone,
                ];
            });
        return response()->json($events);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'date' => ['required','date'],
            'time' => ['required','date_format:H:i'],
            'duration_min' => ['nullable','integer','min:10','max:480'],
            'title' => ['nullable','string','max:100'],
            'appointment_type' => ['nullable','string','max:100'],
            'channel' => ['nullable','string','max:100'],
            'notes' => ['nullable','string','max:1000'],
            'dni' => ['nullable','string','max:20'],
            'patient_name' => ['nullable','string','max:255'],
            'patient_age' => ['nullable','integer','min:0','max:120'],
            'phone' => ['nullable','string','max:30'],
        ]);

        $startAt = Carbon::parse($data['date'].' '.$data['time']);
        // Block past dates compared to current time
        if ($startAt->lt(now()->startOfMinute())) {
            return response()->json(['message' => 'No se puede seleccionar una fecha anterior a la actual.'], 422);
        }
        $duration = (int)($data['duration_min'] ?? 30);
        $endAt = (clone $startAt)->addMinutes($duration);

        // Overlap validation
        $overlap = Appointment::where('user_id', $user->id)
            ->where(function($q) use ($startAt, $endAt) {
                $q->whereBetween('start_at', [$startAt, $endAt])
                  ->orWhere(function($q2) use ($startAt, $endAt) {
                      $q2->where('start_at', '<', $startAt)
                         ->where('end_at', '>', $startAt);
                  });
            })
            ->exists();
        if ($overlap) {
            return response()->json(['message' => 'Ya tienes una cita que se superpone en ese horario.'], 422);
        }

        $appt = Appointment::create([
            'user_id' => $user->id,
            'dni' => $data['dni'] ?? null,
            'patient_name' => $data['patient_name'] ?? null,
            'patient_age' => $data['patient_age'] ?? null,
            'phone' => $data['phone'] ?? null,
            'title' => $data['title'] ?? 'Cita',
            'start_at' => $startAt,
            'end_at' => $endAt,
            'appointment_type' => $data['appointment_type'] ?? null,
            'duration_min' => $duration,
            'channel' => $data['channel'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return response()->json(['ok' => true, 'id' => $appt->id]);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $user = Auth::user();
        if ($appointment->user_id !== $user->id) {
            abort(403);
        }
        $data = $request->validate([
            'date' => ['required','date'],
            'time' => ['required','date_format:H:i'],
            'duration_min' => ['nullable','integer','min:10','max:480'],
            'title' => ['nullable','string','max:100'],
            'appointment_type' => ['nullable','string','max:100'],
            'channel' => ['nullable','string','max:100'],
            'notes' => ['nullable','string','max:1000'],
        ]);

        $startAt = Carbon::parse($data['date'].' '.$data['time']);
        if ($startAt->lt(now()->startOfMinute())) {
            return response()->json(['message' => 'No se puede seleccionar una fecha anterior a la actual.'], 422);
        }
        $duration = (int)($data['duration_min'] ?? 30);
        $endAt = (clone $startAt)->addMinutes($duration);

        // Overlap validation (exclude current appointment)
        $overlap = Appointment::where('user_id', $user->id)
            ->where('id', '!=', $appointment->id)
            ->where(function($q) use ($startAt, $endAt) {
                $q->whereBetween('start_at', [$startAt, $endAt])
                  ->orWhere(function($q2) use ($startAt, $endAt) {
                      $q2->where('start_at', '<', $startAt)
                         ->where('end_at', '>', $startAt);
                  });
            })
            ->exists();
        if ($overlap) {
            return response()->json(['message' => 'Ya tienes una cita que se superpone en ese horario.'], 422);
        }

        $appointment->update([
            'title' => $data['title'] ?? 'Cita',
            'start_at' => $startAt,
            'end_at' => $endAt,
            'appointment_type' => $data['appointment_type'] ?? null,
            'duration_min' => $duration,
            'channel' => $data['channel'] ?? null,
            'notes' => $data['notes'] ?? null,
            'dni' => $data['dni'] ?? $appointment->dni,
            'patient_name' => $data['patient_name'] ?? $appointment->patient_name,
            'patient_age' => $data['patient_age'] ?? $appointment->patient_age,
            'phone' => $data['phone'] ?? $appointment->phone,
        ]);

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, Appointment $appointment)
    {
        $user = Auth::user();
        if ($appointment->user_id !== $user->id) {
            abort(403);
        }
        $appointment->delete();
        return response()->json(['ok' => true]);
    }

    public function patientLookup(Request $request)
    {
        $user = Auth::user();
        $dni = trim((string)$request->query('dni', ''));
        if ($dni === '') return response()->json([]);
        $latest = Appointment::where('user_id', $user->id)
            ->where('dni', $dni)
            ->orderBy('updated_at', 'desc')
            ->first(['patient_name','patient_age','dni']);
        if ($latest) {
            return response()->json($latest);
        }

        // Fallback externo: API RENIEC (configurable)
        $apiUrl = rtrim((string)env('DNI_API_URL', ''), '/');
        $apiToken = (string)env('DNI_API_TOKEN', '');
        if ($apiUrl !== '') {
            try {
                // Intenta dos formatos comunes: GET /{dni} ó ?dni=
                $url = strpos($apiUrl, '{dni}') !== false ? str_replace('{dni}', $dni, $apiUrl) : ($apiUrl . (str_contains($apiUrl, '?') ? '&' : '?') . 'dni=' . $dni);
                $req = $apiToken ? Http::withToken($apiToken) : Http::withoutVerifying();
                $resp = $req->timeout(6)->get($url);
                if ($resp->ok()) {
                    $data = $resp->json();
                    // Normaliza campos posibles
                    $first = $data['nombres'] ?? $data['nombre'] ?? null;
                    $pat = $data['apellidoPaterno'] ?? $data['apellidopaterno'] ?? $data['apellido_paterno'] ?? $data['apellido'] ?? null;
                    $mat = $data['apellidoMaterno'] ?? $data['apellidomaterno'] ?? $data['apellido_materno'] ?? null;
                    $full = trim(implode(' ', array_filter([$first, $pat, $mat])));
                    $birth = $data['fechaNacimiento'] ?? $data['fecha_nacimiento'] ?? $data['nacimiento'] ?? null;
                    $age = null;
                    if ($birth) {
                        try { $age = Carbon::parse($birth)->age; } catch (\Throwable $e) { $age = null; }
                    }
                    if ($full || $age !== null) {
                        return response()->json([
                            'dni' => $dni,
                            'patient_name' => $full ?: null,
                            'patient_age' => $age,
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                // Ignorar y devolver vacío
            }
        }

        return response()->json([]);
    }

    public function registry(Request $request)
    {
        $user = Auth::user();
        $now = now();
        $upcoming = Appointment::where('user_id', $user->id)
            ->where('start_at', '>=', $now)
            ->orderBy('start_at', 'asc')
            ->paginate(10, ['*'], 'up')
            ->appends($request->query());
        $past = Appointment::where('user_id', $user->id)
            ->where('start_at', '<', $now)
            ->orderBy('start_at', 'desc')
            ->paginate(10, ['*'], 'past')
            ->appends($request->query());
        return view('user.registry', compact('upcoming','past'));
    }
}
