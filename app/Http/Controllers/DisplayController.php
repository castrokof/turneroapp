<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\ServiceType;
use App\Models\ServiceWindow;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DisplayController extends Controller
{
    public function index()
    {
        $settings = [
            'refresh_rate' => SystemSetting::get('display_refresh_rate', 5) * 1000,
            'show_next' => SystemSetting::get('display_show_next', 5),
            'sound_enabled' => SystemSetting::get('display_sound_enabled', true),
            'voice_enabled' => SystemSetting::get('display_voice_enabled', true),
            'business_name' => SystemSetting::get('business_name', 'Sistema de Turnos'),
        ];

        return view('display.index', compact('settings'));
    }

    public function byService($serviceTypeId = null)
    {
        $serviceType = $serviceTypeId ? ServiceType::find($serviceTypeId) : null;

        $settings = [
            'refresh_rate' => SystemSetting::get('display_refresh_rate', 5) * 1000,
            'show_next' => SystemSetting::get('display_show_next', 5),
            'sound_enabled' => SystemSetting::get('display_sound_enabled', true),
            'voice_enabled' => SystemSetting::get('display_voice_enabled', true),
            'business_name' => SystemSetting::get('business_name', 'Sistema de Turnos'),
        ];

        return view('display.by-service', compact('settings', 'serviceType'));
    }

    public function getData(Request $request)
    {
        $serviceTypeId = $request->get('service_type_id');
        $showNext = $request->get('show_next', 5);

        // Cache key based on parameters (cache for 3 seconds to reduce DB load with multiple TVs)
        $cacheKey = "display_data_{$serviceTypeId}_{$showNext}";

        $data = Cache::remember($cacheKey, 3, function () use ($serviceTypeId, $showNext) {
            // Get currently being attended
            $currentQuery = Queue::today()
                ->whereIn('status', ['called', 'in_progress'])
                ->with(['serviceType', 'serviceWindow']);

            if ($serviceTypeId) {
                $currentQuery->where('service_type_id', $serviceTypeId);
            }

            $currentQueues = $currentQuery
                ->orderBy('called_at', 'desc')
                ->get()
                ->map(function ($queue) {
                    return [
                        'id' => $queue->id,
                        'ticket_number' => $queue->ticket_number,
                        'service_name' => $queue->serviceType->name,
                        'service_color' => $queue->serviceType->color,
                        'window_name' => $queue->serviceWindow ? $queue->serviceWindow->name : '-',
                        'window_code' => $queue->serviceWindow ? $queue->serviceWindow->code : '-',
                        'status' => $queue->status,
                        'called_at' => $queue->called_at ? $queue->called_at->format('H:i:s') : null,
                        'priority' => $queue->priority,
                    ];
                });

            // Get next pending
            $pendingQuery = Queue::today()
                ->pending()
                ->with(['serviceType'])
                ->orderByPriority();

            if ($serviceTypeId) {
                $pendingQuery->where('service_type_id', $serviceTypeId);
            }

            $pendingQueues = $pendingQuery
                ->limit($showNext)
                ->get()
                ->map(function ($queue) {
                    return [
                        'id' => $queue->id,
                        'ticket_number' => $queue->ticket_number,
                        'service_name' => $queue->serviceType->name,
                        'service_color' => $queue->serviceType->color,
                        'priority' => $queue->priority,
                        'priority_label' => $queue->priority_label,
                        'created_at' => $queue->created_at->format('H:i:s'),
                    ];
                });

            // Get statistics
            $statsQuery = Queue::today();
            if ($serviceTypeId) {
                $statsQuery->where('service_type_id', $serviceTypeId);
            }

            $stats = [
                'total' => (clone $statsQuery)->count(),
                'pending' => (clone $statsQuery)->pending()->count(),
                'completed' => (clone $statsQuery)->where('status', 'completed')->count(),
                'absent' => (clone $statsQuery)->where('status', 'absent')->count(),
            ];

            // Active windows
            $windows = ServiceWindow::active()
                ->with('currentAgent')
                ->get()
                ->map(function ($window) {
                    $currentQueue = $window->getCurrentQueue();
                    return [
                        'id' => $window->id,
                        'name' => $window->name,
                        'code' => $window->code,
                        'agent' => $window->currentAgent ? $window->currentAgent->name : '-',
                        'current_ticket' => $currentQueue ? $currentQueue->ticket_number : null,
                        'status' => $currentQueue ? $currentQueue->status : 'free',
                    ];
                });

            // Last called (for sound notification)
            $lastCalled = Queue::today()
                ->where('status', 'called')
                ->with(['serviceType', 'serviceWindow'])
                ->orderBy('called_at', 'desc')
                ->first();

            return [
                'current' => $currentQueues,
                'pending' => $pendingQueues,
                'stats' => $stats,
                'windows' => $windows,
                'last_called' => $lastCalled ? [
                    'id' => $lastCalled->id,
                    'ticket_number' => $lastCalled->ticket_number,
                    'window' => $lastCalled->serviceWindow ? $lastCalled->serviceWindow->name : '',
                    'called_at' => $lastCalled->called_at->timestamp,
                    'service_name' => $lastCalled->serviceType ? $lastCalled->serviceType->name : 'Servicio',
                    'service_color' => $lastCalled->serviceType ? $lastCalled->serviceType->color : '#1e3c72',
                ] : null,
            ];
        });

        // Add timestamp outside cache
        $data['timestamp'] = now()->timestamp;

        return response()->json($data);
    }

    public function tv()
    {
        $settings = [
            'refresh_rate' => SystemSetting::get('display_refresh_rate', 5) * 1000,
            'show_next' => SystemSetting::get('display_show_next', 8),
            'sound_enabled' => SystemSetting::get('display_sound_enabled', true),
            'voice_enabled' => SystemSetting::get('display_voice_enabled', true),
            'business_name' => SystemSetting::get('business_name', 'Sistema de Turnos'),
            'tv_video_url' => SystemSetting::get('tv_video_url', ''),
            'tv_logo_url' => SystemSetting::get('tv_logo_url', ''),
            'tv_message' => SystemSetting::get('tv_message', ''),
        ];

        return view('display.tv', compact('settings'));
    }

    public function kiosk()
    {
        $serviceTypes = ServiceType::active()->ordered()->get();

        $settings = [
            'business_name' => SystemSetting::get('business_name', 'Sistema de Turnos'),
            'tv_logo_url' => SystemSetting::get('tv_logo_url', ''),
            'kiosk_message' => SystemSetting::get('kiosk_message', ''),
        ];

        return view('display.kiosk', compact('serviceTypes', 'settings'));
    }
}
