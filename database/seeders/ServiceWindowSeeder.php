<?php

namespace Database\Seeders;

use App\Models\ServiceWindow;
use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class ServiceWindowSeeder extends Seeder
{
    public function run()
    {
        $windows = [
            [
                'name' => 'Ventanilla 1',
                'code' => 'V1',
                'description' => 'Ventanilla principal',
                'location' => 'Planta Baja',
                'display_order' => 1,
            ],
            [
                'name' => 'Ventanilla 2',
                'code' => 'V2',
                'description' => 'Ventanilla secundaria',
                'location' => 'Planta Baja',
                'display_order' => 2,
            ],
            [
                'name' => 'Ventanilla 3',
                'code' => 'V3',
                'description' => 'Atención preferencial',
                'location' => 'Planta Baja',
                'display_order' => 3,
            ],
            [
                'name' => 'Caja 1',
                'code' => 'C1',
                'description' => 'Caja de pagos',
                'location' => 'Planta Baja - Sector Caja',
                'display_order' => 4,
            ],
            [
                'name' => 'Información',
                'code' => 'INFO',
                'description' => 'Mesa de información',
                'location' => 'Entrada Principal',
                'display_order' => 5,
            ],
        ];

        // Get all service types for assignment
        $allServices = ServiceType::all();
        $consultaGeneral = ServiceType::where('prefix', 'CG')->first();
        $atencionPref = ServiceType::where('prefix', 'AP')->first();
        $tramites = ServiceType::where('prefix', 'TD')->first();
        $caja = ServiceType::where('prefix', 'CJ')->first();
        $info = ServiceType::where('prefix', 'IN')->first();
        $emergencias = ServiceType::where('prefix', 'EM')->first();

        foreach ($windows as $index => $windowData) {
            $window = ServiceWindow::create($windowData);

            // Assign services based on window
            switch ($window->code) {
                case 'V1':
                    $window->serviceTypes()->attach([$consultaGeneral->id, $tramites->id, $emergencias->id]);
                    break;
                case 'V2':
                    $window->serviceTypes()->attach([$consultaGeneral->id, $tramites->id]);
                    break;
                case 'V3':
                    $window->serviceTypes()->attach([$atencionPref->id, $consultaGeneral->id]);
                    break;
                case 'C1':
                    $window->serviceTypes()->attach([$caja->id]);
                    break;
                case 'INFO':
                    $window->serviceTypes()->attach([$info->id]);
                    break;
            }
        }
    }
}
