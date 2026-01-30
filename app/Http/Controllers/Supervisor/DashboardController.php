<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Queue;
use App\Models\User;
use App\Models\ServiceType;
use App\Models\ServiceWindow;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->filled('date') ? Carbon::parse($request->date) : today();

        // General stats for the day
        $stats = [
            'total_queues' => Queue::whereDate('queue_date', $date)->count(),
            'pending' => Queue::whereDate('queue_date', $date)->where('status', 'pending')->count(),
            'in_progress' => Queue::whereDate('queue_date', $date)->whereIn('status', ['called', 'in_progress'])->count(),
            'completed' => Queue::whereDate('queue_date', $date)->where('status', 'completed')->count(),
            'absent' => Queue::whereDate('queue_date', $date)->where('status', 'absent')->count(),
            'cancelled' => Queue::whereDate('queue_date', $date)->where('status', 'cancelled')->count(),
        ];

        // Average wait time
        $avgWaitTime = Queue::whereDate('queue_date', $date)
            ->whereNotNull('wait_time')
            ->where('wait_time', '>', 0)
            ->avg('wait_time');
        $stats['avg_wait_time'] = $avgWaitTime ? gmdate('i:s', (int) $avgWaitTime) : '00:00';

        // Average service time
        $avgServiceTime = Queue::whereDate('queue_date', $date)
            ->whereNotNull('service_time')
            ->where('service_time', '>', 0)
            ->avg('service_time');
        $stats['avg_service_time'] = $avgServiceTime ? gmdate('i:s', (int) $avgServiceTime) : '00:00';

        // Agent performance
        $agents = User::where('role', 'agent')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($agent) use ($date) {
                $queues = Queue::whereDate('queue_date', $date)
                    ->where('agent_id', $agent->id);

                $completed = (clone $queues)->where('status', 'completed')->count();
                $absent = (clone $queues)->where('status', 'absent')->count();
                $total = (clone $queues)->whereIn('status', ['completed', 'absent', 'in_progress', 'called'])->count();

                $avgWait = (clone $queues)->whereNotNull('wait_time')->where('wait_time', '>', 0)->avg('wait_time');
                $avgService = (clone $queues)->whereNotNull('service_time')->where('service_time', '>', 0)->avg('service_time');

                return (object) [
                    'id' => $agent->id,
                    'name' => $agent->name,
                    'window' => $agent->serviceWindow ? $agent->serviceWindow->name : '-',
                    'total_attended' => $total,
                    'completed' => $completed,
                    'absent' => $absent,
                    'avg_wait_time' => $avgWait ? gmdate('i:s', (int) $avgWait) : '00:00',
                    'avg_service_time' => $avgService ? gmdate('i:s', (int) $avgService) : '00:00',
                    'is_online' => $agent->serviceWindow && $agent->serviceWindow->status === 'active',
                ];
            });

        // Queues by service type
        $queuesByService = ServiceType::withCount(['queues' => function ($query) use ($date) {
            $query->whereDate('queue_date', $date);
        }])->active()->get();

        // Queues by hour (for chart)
        $queuesByHour = Queue::whereDate('queue_date', $date)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        $hourlyData = [];
        for ($i = 0; $i < 24; $i++) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $hourlyData[$hour] = $queuesByHour[$i] ?? 0;
        }

        // Status distribution for pie chart
        $statusDistribution = [
            'Completados' => $stats['completed'],
            'En espera' => $stats['pending'],
            'En atención' => $stats['in_progress'],
            'Ausentes' => $stats['absent'],
            'Cancelados' => $stats['cancelled'],
        ];

        // Recent activity - last 20 queues
        $recentQueues = Queue::with(['client', 'serviceType', 'serviceWindow', 'agent'])
            ->whereDate('queue_date', $date)
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();

        return view('supervisor.dashboard', compact(
            'stats',
            'agents',
            'queuesByService',
            'hourlyData',
            'statusDistribution',
            'recentQueues',
            'date'
        ));
    }

    public function agentDetail(Request $request, User $agent)
    {
        $date = $request->filled('date') ? Carbon::parse($request->date) : today();

        $queues = Queue::with(['client', 'serviceType', 'serviceWindow'])
            ->whereDate('queue_date', $date)
            ->where('agent_id', $agent->id)
            ->orderBy('updated_at', 'desc')
            ->get();

        $stats = [
            'total' => $queues->count(),
            'completed' => $queues->where('status', 'completed')->count(),
            'absent' => $queues->where('status', 'absent')->count(),
            'in_progress' => $queues->whereIn('status', ['called', 'in_progress'])->count(),
        ];

        $avgWait = $queues->whereNotNull('wait_time')->where('wait_time', '>', 0)->avg('wait_time');
        $avgService = $queues->whereNotNull('service_time')->where('service_time', '>', 0)->avg('service_time');

        $stats['avg_wait_time'] = $avgWait ? gmdate('i:s', (int) $avgWait) : '00:00';
        $stats['avg_service_time'] = $avgService ? gmdate('i:s', (int) $avgService) : '00:00';

        return view('supervisor.agent-detail', compact('agent', 'queues', 'stats', 'date'));
    }
}
