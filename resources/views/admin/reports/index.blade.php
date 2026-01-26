@extends('layouts.app')

@section('title', 'Reportes - Sistema de Turnos')
@section('page-title', 'Reportes y Estadísticas')

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-filter mr-2"></i>Filtros de Reporte
    </div>
    <div class="card-body">
        <form action="{{ route('admin.reports.generate') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="date_from">Desde</label>
                    <input type="date" name="date_from" id="date_from" class="form-control"
                           value="{{ $validated['date_from'] ?? now()->startOfMonth()->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="date_to">Hasta</label>
                    <input type="date" name="date_to" id="date_to" class="form-control"
                           value="{{ $validated['date_to'] ?? now()->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="service_type_id">Tipo de Servicio</label>
                    <select name="service_type_id" id="service_type_id" class="form-control">
                        <option value="">Todos</option>
                        @foreach($serviceTypes as $service)
                            <option value="{{ $service->id }}" {{ ($validated['service_type_id'] ?? '') == $service->id ? 'selected' : '' }}>
                                {{ $service->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="agent_id">Agente</label>
                    <select name="agent_id" id="agent_id" class="form-control">
                        <option value="">Todos</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ ($validated['agent_id'] ?? '') == $agent->id ? 'selected' : '' }}>
                                {{ $agent->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="service_window_id">Ventanilla</label>
                    <select name="service_window_id" id="service_window_id" class="form-control">
                        <option value="">Todas</option>
                        @foreach($windows as $window)
                            <option value="{{ $window->id }}" {{ ($validated['service_window_id'] ?? '') == $window->id ? 'selected' : '' }}>
                                {{ $window->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="status">Estado</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">Todos</option>
                        <option value="completed" {{ ($validated['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completado</option>
                        <option value="absent" {{ ($validated['status'] ?? '') === 'absent' ? 'selected' : '' }}>Ausente</option>
                        <option value="cancelled" {{ ($validated['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                        <option value="pending" {{ ($validated['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pendiente</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search mr-1"></i>Generar Reporte
                    </button>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <a class="btn btn-success btn-block" onclick="exportReport()">
                        <i class="fas fa-file-excel mr-1"></i>Exportar CSV
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@isset($stats)
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-primary">{{ $stats['total'] }}</h3>
                <small class="text-muted">Total Turnos</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-success">{{ $stats['completed'] }}</h3>
                <small class="text-muted">Completados</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-danger">{{ $stats['absent'] }}</h3>
                <small class="text-muted">Ausentes</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-info">{{ $stats['completion_rate'] }}%</h3>
                <small class="text-muted">Tasa Completados</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-warning">{{ $stats['avg_wait_time'] }}</h3>
                <small class="text-muted">Espera Prom.</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-secondary">{{ $stats['avg_service_time'] }}</h3>
                <small class="text-muted">Atención Prom.</small>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Turnos por Servicio</div>
            <div class="card-body">
                <canvas id="chartByService" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Turnos por Hora</div>
            <div class="card-body">
                <canvas id="chartByHour" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-table mr-2"></i>Detalle de Turnos ({{ $queues->count() }} registros)
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
            <table class="table table-hover table-sm mb-0">
                <thead class="thead-light" style="position: sticky; top: 0;">
                    <tr>
                        <th>Fecha</th>
                        <th>Turno</th>
                        <th>Cliente</th>
                        <th>Servicio</th>
                        <th>Ventanilla</th>
                        <th>Agente</th>
                        <th>Estado</th>
                        <th>Espera</th>
                        <th>Atención</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($queues as $queue)
                        <tr>
                            <td>{{ $queue->queue_date->format('d/m') }}</td>
                            <td><strong>{{ $queue->ticket_number }}</strong></td>
                            <td>{{ $queue->client ? $queue->client->full_name : '-' }}</td>
                            <td>
                                <span class="badge" style="background-color: {{ $queue->serviceType->color }}; color: #fff;">
                                    {{ $queue->serviceType->prefix }}
                                </span>
                            </td>
                            <td>{{ $queue->serviceWindow->name ?? '-' }}</td>
                            <td>{{ $queue->agent->name ?? '-' }}</td>
                            <td><span class="badge badge-{{ $queue->status_color }}">{{ $queue->status_label }}</span></td>
                            <td>{{ $queue->getWaitTimeFormatted() }}</td>
                            <td>{{ $queue->getServiceTimeFormatted() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No se encontraron turnos</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endisset
@endsection

@isset($stats)
@push('scripts')
<script>
    // Chart by Service
    $.get('{{ route("admin.reports.chart") }}', {
        date_from: '{{ $validated["date_from"] }}',
        date_to: '{{ $validated["date_to"] }}',
        type: 'by_service'
    }, function(data) {
        new Chart(document.getElementById('chartByService'), {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
                    backgroundColor: data.colors
                }]
            }
        });
    });

    // Chart by Hour
    $.get('{{ route("admin.reports.chart") }}', {
        date_from: '{{ $validated["date_from"] }}',
        date_to: '{{ $validated["date_to"] }}',
        type: 'by_hour'
    }, function(data) {
        new Chart(document.getElementById('chartByHour'), {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Turnos',
                    data: data.data,
                    backgroundColor: 'rgba(102, 126, 234, 0.8)'
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    });

    function exportReport() {
    const params = new URLSearchParams({
        date_from: document.getElementById('date_from').value,
        date_to: document.getElementById('date_to').value,
        service_type_id: document.getElementById('service_type_id').value || '',
        agent_id: document.getElementById('agent_id').value || '',
        service_window_id: document.getElementById('service_window_id').value || '',
        status: document.getElementById('status').value || '',
        format: 'csv'
    });
    
    window.location.href = '{{ route("admin.reports.export") }}?' + params.toString();
}
</script>
@endpush
@endisset
