<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Client;
use App\Models\Queue;
use App\Models\ServiceType;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function index()
    {
        $serviceTypes = ServiceType::active()->ordered()->get();

        return view('queue.index', compact('serviceTypes'));
    }

    public function create(Request $request)
    {
        $serviceTypes = ServiceType::active()->ordered()->get();
        $selectedService = $request->get('service');

        return view('queue.create', compact('serviceTypes', 'selectedService'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_type_id' => 'required|exists:service_types,id',
            'client_id' => 'nullable|exists:clients,id',
            'document_number' => 'nullable|string|max:20',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'priority' => 'nullable|in:emergency,priority,scheduled,normal',
            'is_express' => 'boolean',
        ]);

        $serviceType = ServiceType::findOrFail($validated['service_type_id']);

        // Check if daily limit reached
        if ($serviceType->isDailyLimitReached()) {
            return $this->errorResponse('Se ha alcanzado el límite diario de turnos para este servicio.');
        }

        // Check business hours
        if (!$this->isWithinBusinessHours()) {
            return $this->errorResponse('Fuera del horario de atención.');
        }

        // Get or create client
        $clientId = $validated['client_id'] ?? null;

        if (!$clientId && !empty($validated['document_number'])) {
            $client = Client::where('document_number', $validated['document_number'])->first();

            if (!$client && !empty($validated['first_name']) && !empty($validated['last_name'])) {
                $client = Client::create([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'document_type' => 'dni',
                    'document_number' => $validated['document_number'],
                ]);
            }

            $clientId = $client ? $client->id : null;
        }

        // Determine priority
        $priority = $validated['priority'] ?? 'normal';

        if ($clientId && $priority === 'normal') {
            $client = Client::find($clientId);
            if ($client && $client->hasPriority()) {
                $priority = 'priority';
            }
        }

        // Generate ticket number
        $ticketNumber = Queue::generateTicketNumber($validated['service_type_id']);

        // Create queue
        $queue = Queue::create([
            'ticket_number' => $ticketNumber,
            'client_id' => $clientId,
            'service_type_id' => $validated['service_type_id'],
            'priority' => $priority,
            'is_express' => $request->has('is_express'),
            'queue_date' => today(),
        ]);

        $queue->load('serviceType', 'client');

        AuditLog::log('queue_created', $queue, null, $queue->toArray());

        // Calculate estimated wait time
        $pendingCount = Queue::today()
            ->pending()
            ->where('service_type_id', $validated['service_type_id'])
            ->count();

        $estimatedWait = $pendingCount * $serviceType->estimated_time;

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Turno generado correctamente.',
                'data' => [
                    'queue' => $queue,
                    'ticket_number' => $ticketNumber,
                    'service_name' => $serviceType->name,
                    'service_color' => $serviceType->color,
                    'priority' => $queue->priority_label,
                    'estimated_wait' => $estimatedWait,
                    'pending_count' => $pendingCount,
                    'created_at' => $queue->created_at->format('H:i:s'),
                ],
            ]);
        }

        return redirect()->route('queue.ticket', $queue)
            ->with('success', 'Turno generado correctamente.');
    }

    public function ticket(Queue $queue)
    {
        $queue->load('serviceType', 'client');

        $pendingCount = Queue::today()
            ->pending()
            ->where('service_type_id', $queue->service_type_id)
            ->where('id', '<', $queue->id)
            ->count();

        $estimatedWait = $pendingCount * $queue->serviceType->estimated_time;

        return view('queue.ticket', compact('queue', 'pendingCount', 'estimatedWait'));
    }

    public function status(Queue $queue)
    {
        $queue->load('serviceType', 'serviceWindow');

        $position = 0;

        if ($queue->status === 'pending') {
            $position = Queue::today()
                ->pending()
                ->where('service_type_id', $queue->service_type_id)
                ->where('id', '<', $queue->id)
                ->count() + 1;
        }

        return response()->json([
            'ticket_number' => $queue->ticket_number,
            'status' => $queue->status,
            'status_label' => $queue->status_label,
            'status_color' => $queue->status_color,
            'service_window' => $queue->serviceWindow ? $queue->serviceWindow->name : null,
            'position' => $position,
            'called_at' => $queue->called_at ? $queue->called_at->format('H:i:s') : null,
        ]);
    }

    public function cancel(Queue $queue)
    {
        if (!in_array($queue->status, ['pending', 'called'])) {
            return $this->errorResponse('No se puede cancelar este turno.');
        }

        $queue->cancel();

        AuditLog::log('queue_cancelled', $queue);

        return $this->successResponse('Turno cancelado correctamente.');
    }

    private function isWithinBusinessHours()
    {
        $openingTime = SystemSetting::get('opening_time', '08:00');
        $closingTime = SystemSetting::get('closing_time', '17:00');

        $now = now()->format('H:i');

        return $now >= $openingTime && $now <= $closingTime;
    }

    public function printTicket(Queue $queue)
    {
        $queue->load('serviceType', 'client');

        return view('queue.print', compact('queue'));
    }
}
