<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        // For now, redirect to dashboard where the calendar lives
        return redirect()->route('dashboard');
    }

    public function events(Request $request)
    {
        // Shared view: all authenticated users can see events
        $events = Appointment::query()
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
            'patient_first_name' => ['required','string','max:255'],
            'patient_last_name' => ['required','string','max:255'],
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

        // Compose patient_name from first/last if provided
        $composedName = trim(($data['patient_first_name'] ?? '') . ' ' . ($data['patient_last_name'] ?? ''));
        if ($composedName === '') { $composedName = $data['patient_name'] ?? null; }

        $appt = Appointment::create([
            'user_id' => $user->id,
            'dni' => $data['dni'] ?? null,
            'patient_name' => $composedName,
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
        // Shared editing allowed for authenticated users
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

        // Overlap validation (exclude current appointment) - check against same owner only
        $overlap = Appointment::where('user_id', $appointment->user_id)
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

        $composedName = trim(($data['patient_first_name'] ?? '') . ' ' . ($data['patient_last_name'] ?? ''));
        if ($composedName === '') { $composedName = $data['patient_name'] ?? $appointment->patient_name; }

        $appointment->update([
            'title' => $data['title'] ?? 'Cita',
            'start_at' => $startAt,
            'end_at' => $endAt,
            'appointment_type' => $data['appointment_type'] ?? null,
            'duration_min' => $duration,
            'channel' => $data['channel'] ?? null,
            'notes' => $data['notes'] ?? null,
            'dni' => $data['dni'] ?? $appointment->dni,
            'patient_name' => $composedName,
            'patient_age' => $data['patient_age'] ?? $appointment->patient_age,
            'phone' => $data['phone'] ?? $appointment->phone,
        ]);

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, Appointment $appointment)
    {
        // Shared deletion allowed for authenticated users
        $appointment->delete();
        return response()->json(['ok' => true]);
    }

    // Removed patientLookup and external API usage

    public function registry(Request $request)
    {
        $now = now();
        $upcoming = Appointment::query()->with('user')
            ->where('start_at', '>=', $now)
            ->orderBy('start_at', 'asc')
            ->paginate(10, ['*'], 'up')
            ->appends($request->query());
        $past = Appointment::query()->with('user')
            ->where('start_at', '<', $now)
            ->orderBy('start_at', 'desc')
            ->paginate(10, ['*'], 'past')
            ->appends($request->query());
        // Distinct patients (by DNI if present, else by name)
        // Use aggregates to allow ordering by last appointment
        $patients = Appointment::query()
            ->selectRaw("COALESCE(NULLIF(dni, ''), CONCAT('NAME:', patient_name)) as key_id, MIN(dni) as dni, MIN(patient_name) as patient_name, MIN(patient_age) as patient_age, MAX(start_at) as max_start")
            ->groupBy('key_id')
            ->orderByDesc('max_start')
            ->limit(24)
            ->get();
        return view('user.registry', compact('upcoming','past','patients'));
    }

    public function stats(Request $request)
    {
        $year = (int)($request->query('year', now()->year));
        $month = (int)($request->query('month', 0)); // 1-12 or 0 for whole year
        $data = [];
        if ($month >= 1 && $month <= 12) {
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end = (clone $start)->endOfMonth();
            $counts = Appointment::query()
                ->whereBetween('start_at', [$start, $end])
                ->selectRaw('DATE(start_at) as d, COUNT(*) as c')
                ->groupBy('d')
                ->orderBy('d')
                ->pluck('c','d');
            $labels = [];
            $values = [];
            $cursor = (clone $start);
            while ($cursor <= $end) {
                $d = $cursor->toDateString();
                $labels[] = $cursor->format('d');
                $values[] = (int)($counts[$d] ?? 0);
                $cursor->addDay();
            }
            return response()->json(['granularity' => 'day', 'labels' => $labels, 'values' => $values]);
        } else {
            $start = Carbon::create($year, 1, 1)->startOfYear();
            $end = (clone $start)->endOfYear();
            $counts = Appointment::query()
                ->whereBetween('start_at', [$start, $end])
                ->selectRaw('DATE_FORMAT(start_at, "%Y-%m") as ym, COUNT(*) as c')
                ->groupBy('ym')
                ->orderBy('ym')
                ->pluck('c','ym');
            $labels = [];
            $values = [];
            for ($m = 1; $m <= 12; $m++) {
                $ym = Carbon::create($year, $m, 1)->format('Y-m');
                $labels[] = Carbon::create($year, $m, 1)->locale('es')->isoFormat('MMM');
                $values[] = (int)($counts[$ym] ?? 0);
            }
            return response()->json(['granularity' => 'month', 'labels' => $labels, 'values' => $values]);
        }
    }

    public function exportCsv(Request $request)
    {
        $filename = 'appointments_' . now()->format('Ymd_His') . '.csv';
        $columns = ['Fecha', 'Hora', 'DNI', 'Nombre', 'Edad', 'Tipo', 'Canal', 'Notas', 'Registrado por'];
        $callback = function() use ($columns) {
            $out = fopen('php://output', 'w');
            // Header
            fputcsv($out, $columns);
            // Data
            Appointment::with('user')->orderBy('start_at', 'desc')->chunk(500, function($chunk) use ($out) {
                foreach ($chunk as $a) {
                    $date = optional($a->start_at)->format('Y-m-d');
                    $time = optional($a->start_at)->format('H:i');
                    fputcsv($out, [
                        $date,
                        $time,
                        (string)($a->dni ?? ''),
                        (string)($a->patient_name ?? ''),
                        (string)($a->patient_age ?? ''),
                        (string)($a->appointment_type ?? ''),
                        (string)($a->channel ?? ''),
                        (string)($a->notes ?? ''),
                        optional($a->user)->name ?? '',
                    ]);
                }
            });
            fclose($out);
        };
        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
