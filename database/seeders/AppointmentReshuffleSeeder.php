<?php

namespace Database\Seeders;

use App\Models\Appointment;
use Illuminate\Database\Seeder;

class AppointmentReshuffleSeeder extends Seeder
{
    public function run(): void
    {
        $from = new \DateTime('2025-09-25 00:00:00');
        $to = new \DateTime('now');
        $durations = [30, 45, 60];

        $appointments = Appointment::query()->get();
        foreach ($appointments as $appt) {
            $randTs = mt_rand($from->getTimestamp(), $to->getTimestamp());
            $start = (new \DateTime())->setTimestamp($randTs);
            $duration = $durations[array_rand($durations)];
            $end = (clone $start)->modify("+{$duration} minutes");

            $appt->start_at = $start;
            $appt->end_at = $end;
            $appt->duration_min = $duration;
            $appt->save();
        }
    }
}
