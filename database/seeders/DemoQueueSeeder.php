<?php

namespace Database\Seeders;

use App\Models\Queue;
use App\Models\Client;
use App\Models\ServiceType;
use App\Models\DailyCounter;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DemoQueueSeeder extends Seeder
{
    public function run()
    {
        $clients = Client::all();
        $serviceTypes = ServiceType::all();
        $today = today();

        // Create some demo queues for today
        $demoQueues = [
            ['service' => 'CG', 'status' => 'completed', 'priority' => 'normal', 'client_index' => 0],
            ['service' => 'CG', 'status' => 'completed', 'priority' => 'normal', 'client_index' => 1],
            ['service' => 'AP', 'status' => 'completed', 'priority' => 'priority', 'client_index' => 2],
            ['service' => 'TD', 'status' => 'completed', 'priority' => 'normal', 'client_index' => 3],
            ['service' => 'CJ', 'status' => 'completed', 'priority' => 'normal', 'client_index' => 4],
            ['service' => 'CG', 'status' => 'pending', 'priority' => 'normal', 'client_index' => 5],
            ['service' => 'CG', 'status' => 'pending', 'priority' => 'priority', 'client_index' => 6],
            ['service' => 'TD', 'status' => 'pending', 'priority' => 'normal', 'client_index' => 7],
            ['service' => 'AP', 'status' => 'pending', 'priority' => 'priority', 'client_index' => 0],
            ['service' => 'CJ', 'status' => 'pending', 'priority' => 'normal', 'client_index' => 1],
        ];

        foreach ($demoQueues as $queueData) {
            $serviceType = $serviceTypes->where('prefix', $queueData['service'])->first();
            $client = $clients[$queueData['client_index']] ?? null;

            // Get or create counter
            $counter = DailyCounter::firstOrCreate(
                [
                    'date' => $today,
                    'service_type_id' => $serviceType->id,
                ],
                [
                    'last_number' => 0,
                    'total_generated' => 0,
                ]
            );

            $counter->increment('last_number');
            $counter->increment('total_generated');

            $ticketNumber = $serviceType->prefix . '-' . str_pad($counter->last_number, 3, '0', STR_PAD_LEFT);

            $queue = Queue::create([
                'ticket_number' => $ticketNumber,
                'client_id' => $client ? $client->id : null,
                'service_type_id' => $serviceType->id,
                'priority' => $queueData['priority'],
                'status' => $queueData['status'],
                'queue_date' => $today,
            ]);

            // Add times for completed queues
            if ($queueData['status'] === 'completed') {
                $createdAt = Carbon::now()->subMinutes(rand(60, 180));
                $calledAt = $createdAt->copy()->addMinutes(rand(5, 15));
                $startedAt = $calledAt->copy()->addMinutes(rand(1, 3));
                $completedAt = $startedAt->copy()->addMinutes(rand(5, 20));

                $queue->update([
                    'created_at' => $createdAt,
                    'called_at' => $calledAt,
                    'started_at' => $startedAt,
                    'completed_at' => $completedAt,
                    'wait_time' => $calledAt->diffInSeconds($createdAt),
                    'service_time' => $completedAt->diffInSeconds($startedAt),
                ]);
            }
        }
    }
}
