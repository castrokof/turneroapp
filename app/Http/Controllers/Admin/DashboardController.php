<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Queue;
use App\Models\Client;
use App\Models\ServiceType;
use App\Models\ServiceWindow;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();

        // Today's statistics
        $stats = [
            'total_queues' => Queue::whereDate('queue_date', $today)->count(),
            'pending_queues' => Queue::whereDate('queue_date', $today)->where('status', 'pending')->count(),
            'in_progress' => Queue::whereDate('queue_date', $today)->whereIn('status', ['called', 'in_progress'])->count(),
            'completed' => Queue::whereDate('queue_date', $today)->where('status', 'completed')->count(),
            'absent' => Queue::whereDate('queue_date', $today)->where('status', 'absent')->count(),
            'active_windows' => ServiceWindow::where('status', 'active')->count(),
            'total_clients' => Client::count(),
        ];

        // Average wait time today
        $avgWaitTime = Queue::whereDate('queue_date', $today)
            ->whereNotNull('wait_time')
            ->avg('wait_time');
        $stats['avg_wait_time'] = $avgWaitTime ? gmdate('i:s', $avgWaitTime) : '00:00';

        // Average service time today
        $avgServiceTime = Queue::whereDate('queue_date', $today)
            ->whereNotNull('service_time')
            ->avg('service_time');
        $stats['avg_service_time'] = $avgServiceTime ? gmdate('i:s', $avgServiceTime) : '00:00';

        // Queues by service type
        $queuesByService = ServiceType::withCount(['queues' => function ($query) use ($today) {
            $query->whereDate('queue_date', $today);
        }])->active()->get();

        // Queues by hour (for chart)
        $queuesByHour = Queue::whereDate('queue_date', $today)
            ->selectRaw('STRFTIME("%H", created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        // Fill missing hours
        $hourlyData = [];
        for ($i = 0; $i < 24; $i++) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $hourlyData[$hour] = $queuesByHour[$hour] ?? 0;
        }

        // Active agents
        $activeAgents = User::agents()
            ->active()
            ->whereHas('serviceWindow', function ($q) {
                $q->where('status', 'active');
            })
            ->with('serviceWindow')
            ->get();

        // Recent queues
        $recentQueues = Queue::with(['client', 'serviceType', 'serviceWindow', 'agent'])
            ->whereDate('queue_date', $today)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'queuesByService',
            'hourlyData',
            'activeAgents',
            'recentQueues'
        ));
    }
}
