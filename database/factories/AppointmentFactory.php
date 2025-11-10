<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use Faker\Factory as FakerFactory;

/**
 * @extends Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        // Faker en español (Perú) para nombres y apellidos realistas
        $faker = FakerFactory::create('es_PE');
        // Random datetime between 2025-09-25 00:00:00 and now
        $start = $faker->dateTimeBetween('2025-09-25 00:00:00', 'now');
        $durations = [30, 45, 60];
        $duration = $faker->randomElement($durations);
        $end = (clone $start)->modify("+{$duration} minutes");

        $types = ['Primera vez', 'Control', 'Limpieza', 'Diagnóstico', 'Tratamiento'];
        $topics = ['Limpieza', 'Restauraciones', 'Ortodoncia', 'Estética'];

        // Ensure there is at least one user. Prefer 'mortegas', then 'admin'.
        $userId = User::query()->where('username', 'mortegas')->value('id')
            ?? User::query()->where('username', 'admin')->value('id')
            ?? User::query()->inRandomOrder()->value('id')
            ?? User::factory()->create(['username' => 'seed_admin_'.uniqid(), 'name' => 'Seed Admin', 'approved' => true])->id;

        // DNI peruano: exactamente 8 dígitos (sin ceros a la izquierda improbables)
        $dni = (string) $faker->numberBetween(10000000, 99999999);

        // Nombre: 1 o 2 nombres + 2 apellidos
        $firsts = [];
        $countFirst = $faker->numberBetween(1, 2);
        for ($i = 0; $i < $countFirst; $i++) { $firsts[] = $faker->firstName(); }
        $last1 = $faker->lastName();
        $last2 = $faker->lastName();
        $patientName = implode(' ', array_merge($firsts, [$last1, $last2]));

        // Edad: < 70 años
        $age = $faker->numberBetween(1, 69);

        // Teléfono móvil peruano: 9 dígitos iniciando en 9
        $phone = '9' . str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);

        // Notas: una de las categorías pedidas
        $topic = $faker->randomElement($topics);
        $note = 'Motivo: ' . $topic;

        return [
            'user_id' => $userId,
            'dni' => $dni,
            'patient_name' => $patientName,
            'patient_age' => $age,
            'phone' => $phone,
            'title' => 'Cita',
            'start_at' => $start,
            'end_at' => $end,
            'appointment_type' => $faker->randomElement($types),
            'duration_min' => $duration,
            'channel' => 'SMS',
            'notes' => $note,
        ];
    }
}
