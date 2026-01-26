<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Queue;
use App\Models\ServiceType;
use App\Models\ServiceWindow;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $serviceTypes = ServiceType::active()->ordered()->get();
        $agents = User::agents()->active()->get();
        $windows = ServiceWindow::ordered()->get();

        return view('admin.reports.index', compact('serviceTypes', 'agents', 'windows'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'service_type_id' => 'nullable|exists:service_types,id',
            'agent_id' => 'nullable|exists:users,id',
            'service_window_id' => 'nullable|exists:service_windows,id',
            'status' => 'nullable|in:pending,called,in_progress,completed,absent,transferred,cancelled',
        ]);

        $query = Queue::with(['client', 'serviceType', 'serviceWindow', 'agent'])
            ->whereBetween('queue_date', [$validated['date_from'], $validated['date_to']]);

        if (!empty($validated['service_type_id'])) {
            $query->where('service_type_id', $validated['service_type_id']);
        }

        if (!empty($validated['agent_id'])) {
            $query->where('agent_id', $validated['agent_id']);
        }

        if (!empty($validated['service_window_id'])) {
            $query->where('service_window_id', $validated['service_window_id']);
        }

        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        $queues = $query->orderBy('queue_date')->orderBy('created_at')->get();

        // Calculate statistics
        $stats = $this->calculateStats($queues);

        // Get service types and agents for filters
        $serviceTypes = ServiceType::active()->ordered()->get();
        $agents = User::agents()->active()->get();
        $windows = ServiceWindow::ordered()->get();

        return view('admin.reports.index', compact(
            'queues',
            'stats',
            'serviceTypes',
            'agents',
            'windows',
            'validated'
        ));
    }

    public function export(Request $request)
    {


        $validated = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'service_type_id' => 'nullable|exists:service_types,id',
            'agent_id' => 'nullable|exists:users,id',
            'format' => 'required|in:csv,excel',
        ]);

        $query = Queue::with(['client', 'serviceType', 'serviceWindow', 'agent'])
            ->whereBetween('queue_date', [$validated['date_from'], $validated['date_to']]);

        if (!empty($validated['service_type_id'])) {
            $query->where('service_type_id', $validated['service_type_id']);
        }

        if (!empty($validated['agent_id'])) {
            $query->where('agent_id', $validated['agent_id']);
        }

        $queues = $query->orderBy('queue_date')->orderBy('created_at')->get();

        $filename = 'reporte_turnos_' . $validated['date_from'] . '_' . $validated['date_to'];

        if ($validated['format'] === 'csv') {
            return $this->exportCsv($queues, $filename);
        }

        return $this->exportCsv($queues, $filename); // Fallback to CSV
    }

    private function exportCsv($queues, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];

        $callback = function () use ($queues) {
            $file = fopen('php://output', 'w');

            // BOM for Excel UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Headers
            fputcsv($file, [
                'Fecha',
                'Turno',
                'Cliente',
                'Documento',
                'Servicio',
                'Ventanilla',
                'Agente',
                'Prioridad',
                'Estado',
                'Hora Creación',
                'Hora Llamado',
                'Hora Inicio',
                'Hora Fin',
                'Espera (min)',
                'Atención (min)',
            ]);

            foreach ($queues as $queue) {
                fputcsv($file, [
                    $queue->queue_date->format('Y-m-d'),
                    $queue->ticket_number,
                    $queue->client ? $queue->client->full_name : 'Sin registrar',
                    $queue->client ? $queue->client->document_number : '-',
                    $queue->serviceType->name,
                    $queue->serviceWindow ? $queue->serviceWindow->name : '-',
                    $queue->agent ? $queue->agent->name : '-',
                    $queue->priority_label,
                    $queue->status_label,
                    $queue->created_at->format('H:i:s'),
                    $queue->called_at ? $queue->called_at->format('H:i:s') : '-',
                    $queue->started_at ? $queue->started_at->format('H:i:s') : '-',
                    $queue->completed_at ? $queue->completed_at->format('H:i:s') : '-',
                    $queue->wait_time ? round($queue->wait_time / 60, 1) : '-',
                    $queue->service_time ? round($queue->service_time / 60, 1) : '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function calculateStats($queues)
    {
        $total = $queues->count();

        if ($total === 0) {
            return [
                'total' => 0,
                'completed' => 0,
                'absent' => 0,
                'cancelled' => 0,
                'completion_rate' => 0,
                'absence_rate' => 0,
                'avg_wait_time' => '00:00',
                'avg_service_time' => '00:00',
                'max_wait_time' => '00:00',
                'max_service_time' => '00:00',
            ];
        }

        $completed = $queues->where('status', 'completed')->count();
        $absent = $queues->where('status', 'absent')->count();
        $cancelled = $queues->where('status', 'cancelled')->count();

        $avgWaitTime = $queues->whereNotNull('wait_time')->avg('wait_time') ?? 0;
        $avgServiceTime = $queues->whereNotNull('service_time')->avg('service_time') ?? 0;
        $maxWaitTime = $queues->whereNotNull('wait_time')->max('wait_time') ?? 0;
        $maxServiceTime = $queues->whereNotNull('service_time')->max('service_time') ?? 0;

        return [
            'total' => $total,
            'completed' => $completed,
            'absent' => $absent,
            'cancelled' => $cancelled,
            'completion_rate' => round(($completed / $total) * 100, 1),
            'absence_rate' => round(($absent / $total) * 100, 1),
            'avg_wait_time' => gmdate('i:s', $avgWaitTime),
            'avg_service_time' => gmdate('i:s', $avgServiceTime),
            'max_wait_time' => gmdate('i:s', $maxWaitTime),
            'max_service_time' => gmdate('i:s', $maxServiceTime),
        ];
    }

    public function chartData(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'type' => 'required|in:by_service,by_agent,by_hour,by_day',
        ]);

        $dateFrom = $validated['date_from'];
        $dateTo = $validated['date_to'];

        switch ($validated['type']) {
            case 'by_service':
                return $this->chartByService($dateFrom, $dateTo);
            case 'by_agent':
                return $this->chartByAgent($dateFrom, $dateTo);
            case 'by_hour':
                return $this->chartByHour($dateFrom, $dateTo);
            case 'by_day':
                return $this->chartByDay($dateFrom, $dateTo);
        }
    }

    private function chartByService($dateFrom, $dateTo)
    {
        $data = ServiceType::withCount(['queues' => function ($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('queue_date', [$dateFrom, $dateTo]);
        }])->get();

        return response()->json([
            'labels' => $data->pluck('name'),
            'data' => $data->pluck('queues_count'),
            'colors' => $data->pluck('color'),
        ]);
    }

    private function chartByAgent($dateFrom, $dateTo)
    {
        $data = User::agents()
            ->withCount(['queues' => function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('queue_date', [$dateFrom, $dateTo])
                  ->where('status', 'completed');
            }])
            ->having('queues_count', '>', 0)
            ->get();

        return response()->json([
            'labels' => $data->pluck('name'),
            'data' => $data->pluck('queues_count'),
        ]);
    }

    private function chartByHour($dateFrom, $dateTo)
    {
        $data = Queue::whereBetween('queue_date', [$dateFrom, $dateTo])
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        $labels = [];
        $values = [];

        for ($i = 0; $i < 24; $i++) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $labels[] = $hour . ':00';
            $values[] = $data[$hour] ?? 0;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $values,
        ]);
    }

    private function chartByDay($dateFrom, $dateTo)
    {
        $data = Queue::whereBetween('queue_date', [$dateFrom, $dateTo])
            ->selectRaw('queue_date, COUNT(*) as count')
            ->groupBy('queue_date')
            ->orderBy('queue_date')
            ->pluck('count', 'queue_date')
            ->toArray();

        return response()->json([
            'labels' => array_keys($data),
            'data' => array_values($data),
        ]);
    }
}
