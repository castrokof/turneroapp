<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run()
    {
        $clients = [
            [
                'first_name' => 'Roberto',
                'last_name' => 'Fernández Díaz',
                'document_type' => 'dni',
                'document_number' => '12345678',
                'email' => 'roberto.fernandez@email.com',
                'phone' => '999111222',
                'birth_date' => '1960-05-15',
                'gender' => 'male',
                'is_elderly' => true,
            ],
            [
                'first_name' => 'Carmen',
                'last_name' => 'Silva Mendoza',
                'document_type' => 'dni',
                'document_number' => '87654321',
                'email' => 'carmen.silva@email.com',
                'phone' => '999333444',
                'birth_date' => '1992-08-20',
                'gender' => 'female',
                'is_pregnant' => true,
            ],
            [
                'first_name' => 'José',
                'last_name' => 'Martínez Ruiz',
                'document_type' => 'dni',
                'document_number' => '45678912',
                'email' => 'jose.martinez@email.com',
                'phone' => '999555666',
                'birth_date' => '1985-03-10',
                'gender' => 'male',
                'has_disability' => true,
            ],
            [
                'first_name' => 'Lucía',
                'last_name' => 'Torres Vega',
                'document_type' => 'dni',
                'document_number' => '78912345',
                'email' => 'lucia.torres@email.com',
                'phone' => '999777888',
                'birth_date' => '1995-11-25',
                'gender' => 'female',
            ],
            [
                'first_name' => 'Miguel',
                'last_name' => 'Ángel Castro',
                'document_type' => 'dni',
                'document_number' => '36925814',
                'email' => 'miguel.castro@email.com',
                'phone' => '999999111',
                'birth_date' => '1980-07-08',
                'gender' => 'male',
            ],
            [
                'first_name' => 'Patricia',
                'last_name' => 'Guzmán Flores',
                'document_type' => 'dni',
                'document_number' => '14725836',
                'email' => 'patricia.guzman@email.com',
                'phone' => '998888777',
                'birth_date' => '1975-12-03',
                'gender' => 'female',
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Ramírez López',
                'document_type' => 'passport',
                'document_number' => 'AB123456',
                'email' => 'david.ramirez@email.com',
                'phone' => '997777666',
                'birth_date' => '1990-04-18',
                'gender' => 'male',
            ],
            [
                'first_name' => 'Elena',
                'last_name' => 'Morales Soto',
                'document_type' => 'dni',
                'document_number' => '96385274',
                'email' => 'elena.morales@email.com',
                'phone' => '996666555',
                'birth_date' => '1988-09-30',
                'gender' => 'female',
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}
