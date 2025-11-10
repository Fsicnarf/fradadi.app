<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure at least one user exists (admin preferred)
        $admin = User::query()->where('username', 'admin')->first();
        if (!$admin) {
            $admin = User::factory()->create([
                'username' => 'admin',
                'name' => 'Administrador',
                'approved' => true,
            ]);
        }

        // Create 42 appointments with realistic patient data
        Appointment::factory()->count(42)->create();
    }
}
