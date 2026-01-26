<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin user
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@turnero.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Agent users
        $agents = [
            ['name' => 'María García', 'email' => 'maria@turnero.com'],
            ['name' => 'Juan Pérez', 'email' => 'juan@turnero.com'],
            ['name' => 'Ana López', 'email' => 'ana@turnero.com'],
            ['name' => 'Carlos Rodríguez', 'email' => 'carlos@turnero.com'],
        ];

        foreach ($agents as $agent) {
            User::create([
                'name' => $agent['name'],
                'email' => $agent['email'],
                'password' => Hash::make('password'),
                'role' => 'agent',
                'is_active' => true,
            ]);
        }

        // Viewer user
        User::create([
            'name' => 'Visualizador',
            'email' => 'viewer@turnero.com',
            'password' => Hash::make('password'),
            'role' => 'viewer',
            'is_active' => true,
        ]);
    }
}
