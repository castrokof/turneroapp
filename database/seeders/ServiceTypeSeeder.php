<?php

namespace Database\Seeders;

use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class ServiceTypeSeeder extends Seeder
{
    public function run()
    {
        $services = [
            [
                'name' => 'Consulta General',
                'prefix' => 'CG',
                'color' => '#007bff',
                'estimated_time' => 15,
                'description' => 'Consultas médicas generales',
                'display_order' => 1,
            ],
            [
                'name' => 'Atención Preferencial',
                'prefix' => 'AP',
                'color' => '#28a745',
                'estimated_time' => 20,
                'description' => 'Atención para adultos mayores, embarazadas y personas con discapacidad',
                'display_order' => 2,
            ],
            [
                'name' => 'Trámites Documentarios',
                'prefix' => 'TD',
                'color' => '#ffc107',
                'estimated_time' => 10,
                'description' => 'Gestión de documentos y certificados',
                'display_order' => 3,
            ],
            [
                'name' => 'Caja / Pagos',
                'prefix' => 'CJ',
                'color' => '#17a2b8',
                'estimated_time' => 5,
                'description' => 'Pagos y cobros',
                'display_order' => 4,
            ],
            [
                'name' => 'Información',
                'prefix' => 'IN',
                'color' => '#6c757d',
                'estimated_time' => 8,
                'description' => 'Consultas de información general',
                'display_order' => 5,
            ],
            [
                'name' => 'Emergencias',
                'prefix' => 'EM',
                'color' => '#dc3545',
                'estimated_time' => 30,
                'description' => 'Atención de emergencias',
                'display_order' => 0,
            ],
        ];

        foreach ($services as $service) {
            ServiceType::create($service);
        }
    }
}
