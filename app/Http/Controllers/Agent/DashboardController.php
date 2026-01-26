<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Queue;
use App\Models\ServiceWindow;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $window = $user->serviceWindow;

        if (!$window || $window->status !== 'active') {
            return view('agent.select-window', [
                'windows' => ServiceWindow::available()->ordered()->get(),
            ]);
        }

        $currentQueue = $window->getCurrentQueue();

        $pendingQueues = Queue::today()
            ->pending()
            ->whereHas('serviceType', function ($q) use ($window) {
                $q->whereHas('serviceWindows', function ($q2) use ($window) {
                    $q2->where('service_windows.id', $window->id);
                });
            })
            ->orderByPriority()
            ->limit(20)
            ->get();

        $todayStats = [
            'completed' => Queue::today()->where('agent_id', $user->id)->where('status', 'completed')->count(),
            'absent' => Queue::today()->where('agent_id', $user->id)->where('status', 'absent')->count(),
            'avg_time' => Queue::today()->where('agent_id', $user->id)->whereNotNull('service_time')->avg('service_time'),
        ];

        return view('agent.dashboard', compact('window', 'currentQueue', 'pendingQueues', 'todayStats'));
    }

    public function selectWindow(Request $request)
    {
        $validated = $request->validate([
            'window_id' => 'required|exists:service_windows,id',
        ]);

        $window = ServiceWindow::findOrFail($validated['window_id']);

        if ($window->status === 'active' && $window->current_agent_id !== auth()->id()) {
            return $this->errorResponse('Esta ventanilla ya está siendo utilizada por otro agente.');
        }

        $user = auth()->user();

        // Deactivate previous window if any
        if ($user->serviceWindow && $user->serviceWindow->id !== $window->id) {
            $user->serviceWindow->deactivate();
        }

        // Activate selected window
        $window->activate($user->id);
        $user->update(['service_window_id' => $window->id]);

        AuditLog::log('agent_window_selected', $window);

        return redirect()->route('agent.dashboard')
            ->with('success', 'Ventanilla activada correctamente.');
    }

    public function leaveWindow()
    {
        $user = auth()->user();
        $window = $user->serviceWindow;

        if ($window) {
            $currentQueue = $window->getCurrentQueue();

            if ($currentQueue) {
                return $this->errorResponse('No puede abandonar la ventanilla mientras atiende un turno.');
            }

            $window->deactivate();
            $user->update(['service_window_id' => null]);

            AuditLog::log('agent_window_left', $window);
        }

        return redirect()->route('agent.dashboard')
            ->with('success', 'Ha abandonado la ventanilla.');
    }

    public function callNext()
    {
        $user = auth()->user();
        $window = $user->serviceWindow;

        if (!$window || $window->status !== 'active') {
            return $this->errorResponse('Debe seleccionar una ventanilla primero.');
        }

        // Check if already attending
        $currentQueue = $window->getCurrentQueue();
        if ($currentQueue) {
            return $this->errorResponse('Ya tiene un turno en atención.');
        }

        // Get next queue
        $nextQueue = Queue::today()
            ->pending()
            ->whereHas('serviceType', function ($q) use ($window) {
                $q->whereHas('serviceWindows', function ($q2) use ($window) {
                    $q2->where('service_windows.id', $window->id);
                });
            })
            ->orderByPriority()
            ->first();

        if (!$nextQueue) {
            return $this->errorResponse('No hay turnos pendientes.');
        }

        $nextQueue->call($window->id, $user->id);

        AuditLog::log('queue_called', $nextQueue);

        return response()->json([
            'success' => true,
            'message' => 'Turno llamado correctamente.',
            'data' => [
                'queue' => $nextQueue->load(['client', 'serviceType']),
            ],
        ]);
    }

    public function callSpecific(Queue $queue)
    {
        $user = auth()->user();
        $window = $user->serviceWindow;

        if (!$window || $window->status !== 'active') {
            return $this->errorResponse('Debe seleccionar una ventanilla primero.');
        }

        if ($queue->status !== 'pending') {
            return $this->errorResponse('Este turno no está disponible.');
        }

        // Check if already attending
        $currentQueue = $window->getCurrentQueue();
        if ($currentQueue) {
            return $this->errorResponse('Ya tiene un turno en atención.');
        }

        $queue->call($window->id, $user->id);

        AuditLog::log('queue_called', $queue);

        return response()->json([
            'success' => true,
            'message' => 'Turno llamado correctamente.',
            'data' => [
                'queue' => $queue->load(['client', 'serviceType']),
            ],
        ]);
    }

    public function recallCurrent()
    {
        $user = auth()->user();
        $window = $user->serviceWindow;

        $currentQueue = $window ? $window->getCurrentQueue() : null;

        if (!$currentQueue || $currentQueue->status !== 'called') {
            return $this->errorResponse('No hay turno para rellamar.');
        }

        // Just update called_at to trigger display refresh
        $currentQueue->update(['called_at' => now()]);

        AuditLog::log('queue_recalled', $currentQueue);

        return response()->json([
            'success' => true,
            'message' => 'Turno rellamado.',
            'data' => [
                'queue' => $currentQueue,
            ],
        ]);
    }

    public function startService()
    {
        $user = auth()->user();
        $window = $user->serviceWindow;

        $currentQueue = $window ? $window->getCurrentQueue() : null;

        if (!$currentQueue || $currentQueue->status !== 'called') {
            return $this->errorResponse('No hay turno llamado.');
        }

        $currentQueue->startService();

        AuditLog::log('queue_started', $currentQueue);

        return response()->json([
            'success' => true,
            'message' => 'Atención iniciada.',
            'data' => [
                'queue' => $currentQueue,
            ],
        ]);
    }

    public function completeService(Request $request)
    {
        $user = auth()->user();
        $window = $user->serviceWindow;

        $currentQueue = $window ? $window->getCurrentQueue() : null;

        if (!$currentQueue) {
            return $this->errorResponse('No hay turno en atención.');
        }

        $notes = $request->get('notes');
        $currentQueue->complete($notes);

        AuditLog::log('queue_completed', $currentQueue);

        return response()->json([
            'success' => true,
            'message' => 'Turno finalizado correctamente.',
        ]);
    }

    public function markAbsent()
    {
        $user = auth()->user();
        $window = $user->serviceWindow;

        $currentQueue = $window ? $window->getCurrentQueue() : null;

        if (!$currentQueue) {
            return $this->errorResponse('No hay turno para marcar como ausente.');
        }

        $currentQueue->markAbsent();

        AuditLog::log('queue_absent', $currentQueue);

        return response()->json([
            'success' => true,
            'message' => 'Turno marcado como ausente.',
        ]);
    }

    public function transferQueue(Request $request)
    {
        $validated = $request->validate([
            'window_id' => 'required|exists:service_windows,id',
            'reason' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        $window = $user->serviceWindow;

        $currentQueue = $window ? $window->getCurrentQueue() : null;

        if (!$currentQueue) {
            return $this->errorResponse('No hay turno para transferir.');
        }

        $currentQueue->transfer($validated['window_id'], $validated['reason']);

        AuditLog::log('queue_transferred', $currentQueue);

        return response()->json([
            'success' => true,
            'message' => 'Turno transferido correctamente.',
        ]);
    }

    public function pauseWindow()
    {
        $user = auth()->user();
        $window = $user->serviceWindow;

        if (!$window) {
            return $this->errorResponse('No tiene ventanilla asignada.');
        }

        $currentQueue = $window->getCurrentQueue();
        if ($currentQueue) {
            return $this->errorResponse('No puede pausar mientras atiende un turno.');
        }

        $window->pause();

        AuditLog::log('agent_window_paused', $window);

        return response()->json([
            'success' => true,
            'message' => 'Ventanilla pausada.',
        ]);
    }

    public function resumeWindow()
    {
        $user = auth()->user();
        $window = $user->serviceWindow;

        if (!$window) {
            return $this->errorResponse('No tiene ventanilla asignada.');
        }

        $window->activate($user->id);

        AuditLog::log('agent_window_resumed', $window);

        return response()->json([
            'success' => true,
            'message' => 'Ventanilla activada.',
        ]);
    }

    public function getPendingQueues()
    {
        $user = auth()->user();
        $window = $user->serviceWindow;

        if (!$window) {
            return response()->json(['queues' => []]);
        }

        $queues = Queue::today()
            ->pending()
            ->whereHas('serviceType', function ($q) use ($window) {
                $q->whereHas('serviceWindows', function ($q2) use ($window) {
                    $q2->where('service_windows.id', $window->id);
                });
            })
            ->with(['client', 'serviceType'])
            ->orderByPriority()
            ->limit(20)
            ->get();

        return response()->json([
            'queues' => $queues,
            'count' => $queues->count(),
        ]);
    }

    public function getCurrentStatus()
    {
        $user = auth()->user();
        $window = $user->serviceWindow;

        $currentQueue = $window ? $window->getCurrentQueue() : null;

        return response()->json([
            'window' => $window ? [
                'id' => $window->id,
                'name' => $window->name,
                'status' => $window->status,
            ] : null,
            'current_queue' => $currentQueue ? [
                'id' => $currentQueue->id,
                'ticket_number' => $currentQueue->ticket_number,
                'status' => $currentQueue->status,
                'client' => $currentQueue->client ? $currentQueue->client->full_name : 'Sin registrar',
                'service' => $currentQueue->serviceType->name,
                'priority' => $currentQueue->priority_label,
                'called_at' => $currentQueue->called_at ? $currentQueue->called_at->format('H:i:s') : null,
                'started_at' => $currentQueue->started_at ? $currentQueue->started_at->format('H:i:s') : null,
                'elapsed_time' => $currentQueue->started_at ? now()->diffInSeconds($currentQueue->started_at) : 0,
            ] : null,
        ]);
    }
}
