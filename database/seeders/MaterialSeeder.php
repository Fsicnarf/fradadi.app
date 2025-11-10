<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\User;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::query()->where('username', 'mortegas')->value('id')
            ?? User::query()->where('username', 'admin')->value('id')
            ?? User::query()->inRandomOrder()->value('id');

        if (!$userId) {
            $userId = User::factory()->create([
                'username' => 'seed_user_'.uniqid(),
                'name' => 'Seed User',
                'approved' => true,
            ])->id;
        }

        $items = [
            // Equipos e infraestructura -> Materiales Auxiliares
            ['Sillón dental','Materiales Auxiliares','unidad'],
            ['Unidad dental (turbina, micromotor, jeringa triple, succión)','Materiales Auxiliares','unidad'],
            ['Lámpara de fotocurado','Materiales Auxiliares','unidad'],
            ['Aparato de rayos X intraoral','Materiales Auxiliares','unidad'],
            ['Autoclave','Materiales Auxiliares','unidad'],
            ['Compresor de aire','Materiales Auxiliares','unidad'],
            ['Destilador de agua','Materiales Auxiliares','unidad'],

            // Instrumental -> Materiales Auxiliares
            ['Espejo de exploración','Materiales Auxiliares','unidad'],
            ['Sonda exploradora','Materiales Auxiliares','unidad'],
            ['Pinza de curación/algodonera','Materiales Auxiliares','unidad'],
            ['Bandeja de instrumental','Materiales Auxiliares','unidad'],
            ['Cucharilla de dentina','Materiales Auxiliares','unidad'],
            ['Fórceps','Materiales Auxiliares','unidad'],
            ['Portaagujas','Materiales Auxiliares','unidad'],
            ['Bisturí','Materiales Auxiliares','unidad'],
            ['Tijeras','Materiales Auxiliares','unidad'],
            ['Limas','Materiales Auxiliares','juego'],

            // Bioseguridad e insumos de higiene -> Materiales Preventivos
            ['Guantes','Materiales Preventivos','caja'],
            ['Mascarillas','Materiales Preventivos','caja'],
            ['Batas desechables','Materiales Preventivos','paquete'],
            ['Gafas de protección','Materiales Preventivos','unidad'],
            ['Campos de trabajo','Materiales Preventivos','paquete'],
            ['Desinfectantes de superficies','Materiales Preventivos','botella'],
            ['Jabón germicida','Materiales Preventivos','botella'],
            ['Bolsas de esterilización','Materiales Preventivos','paquete'],
            ['Cepillos interdentales','Materiales Preventivos','paquete'],
            ['Hilo dental','Materiales Preventivos','paquete'],
            ['Enjuagues bucales','Materiales Preventivos','botella'],

            // Anestesia y varios
            ['Cartuchos de anestesia','Materiales Auxiliares','caja'],
            ['Agujas dentales','Materiales Auxiliares','caja'],
            ['Materiales de impresión','Materiales Auxiliares','kit'],
            ['Algodón','Materiales Auxiliares','paquete'],
            ['Gasas','Materiales Auxiliares','paquete'],
            ['Eyector/Aspirador desechable','Materiales Auxiliares','paquete'],

            // Restaurativos
            ['Amalgamas','Materiales Restaurativos','juego'],
            ['Ionómeros de vidrio','Materiales Restaurativos','juego'],
            ['Cementos dentales','Materiales Restaurativos','juego'],

            // Composites
            ['Resinas compuestas','Composites','juego'],

            // Mobiliario -> Materiales Auxiliares
            ['Cajoneras/Armarios','Materiales Auxiliares','unidad'],
            ['Escritorio','Materiales Auxiliares','unidad'],
            ['Sillas de oficina','Materiales Auxiliares','unidad'],
            ['Sillas de sala de espera','Materiales Auxiliares','unidad'],
            ['Mesa de sala de espera','Materiales Auxiliares','unidad'],
            ['Mostrador de recepción','Materiales Auxiliares','unidad'],
            ['Sistema informático','Materiales Auxiliares','unidad'],
            ['Contenedor residuos biológicos','Materiales Auxiliares','unidad'],
            ['Contenedor cortopunzantes','Materiales Auxiliares','unidad'],
            ['Contenedor residuos comunes','Materiales Auxiliares','unidad'],
        ];

        foreach ($items as [$name,$cat,$unit]) {
            $min = random_int(1, 2); // mínimo 1–2
            $qty = max(0, $min + random_int(-1, 8)); // cantidad variable, puede ser menor al mínimo para probar alerta
            Material::updateOrCreate(
                ['user_id' => $userId, 'name' => $name],
                [
                    'category' => $cat,
                    'quantity' => $qty,
                    'min_quantity' => $min,
                    'unit' => $unit,
                    'notes' => null,
                ]
            );
        }
    }
}
