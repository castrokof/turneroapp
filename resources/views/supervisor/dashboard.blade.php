@extends('layouts.app')

@section('title', 'Supervisor - Sistema de Turnos')
@section('page-title', 'Dashboard Supervisor')

@section('content')
<!-- Date filter -->
<div class="row mb-3">
    <div class="col-md-4">
        <form action="{{ route('supervisor.dashboard') }}" method="GET" class="form-inline">
            <label class="mr-2"><i class="fas fa-calendar-alt mr-1"></i>Fecha:</label>
            <input type="date" name="date" class="form-control mr-2" value="{{ $date->format('Y-m-d') }}">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search mr-1"></i>Consultar</button>
        </form>
    </div>
    <div class="col-md-8 text-right">
        <span class="text-muted">
            <i class="fas fa-clock mr-1"></i>{{ $date->format('d/m/Y') }}
            @if($date->isToday())
                <span class="badge badge-success ml-1">Hoy</span>
            @endif
        </span>
    </div>
</div>

<!-- General Stats -->
<div class="row mb-4">
    <div class="col-md-2 mb-3">
        <div class="card stat-card primary">
            <div class="card-body text-center">
                <div class="stat-value text-primary">{{ $stats['total_queues'] }}</div>
                <div class="stat-label">Total Turnos</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card stat-card warning">
            <div class="card-body text-center">
                <div class="stat-value text-warning">{{ $stats['pending'] }}</div>
                <div class="stat-label">En Espera</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card stat-card info">
            <div class="card-body text-center">
                <div class="stat-value text-info">{{ $stats['in_progress'] }}</div>
                <div class="stat-label">En Atenci&oacute;n</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card stat-card success">
            <div class="card-body text-center">
                <div class="stat-value text-success">{{ $stats['completed'] }}</div>
                <div class="stat-label">Completados</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card stat-card danger">
            <div class="card-body text-center">
                <div class="stat-value text-danger">{{ $stats['absent'] }}</div>
                <div class="stat-label">Ausentes</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card stat-card secondary" style="border-left: 4px solid #6c757d;">
            <div class="card-body text-center">
                <div class="stat-value text-secondary">{{ $stats['cancelled'] }}</div>
                <div class="stat-label">Cancelados</div>
            </div>
        </div>
    </div>
</div>

<!-- Avg times -->
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card stat-card primary">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3">
                    <i class="fas fa-hourglass-half fa-2x text-primary"></i>
                </div>
                <div>
                    <div class="stat-value text-primary" style="font-size: 1.5rem;">{{ $stats['avg_wait_time'] }}</div>
                    <div class="stat-label">Tiempo Espera Promedio</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card stat-card info">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3">
                    <i class="fas fa-user-clock fa-2x text-info"></i>
                </div>
                <div>
                    <div class="stat-value text-info" style="font-size: 1.5rem;">{{ $stats['avg_service_time'] }}</div>
                    <div class="stat-label">Tiempo Atenci&oacute;n Promedio</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Charts -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-bar mr-2"></i>Turnos por Hora
            </div>
            <div class="card-body">
                <canvas id="hourlyChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-pie mr-2"></i>Distribuci&oacute;n de Estados
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Agent Performance Table -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-users mr-2"></i>Rendimiento por Agente</span>
                <span class="badge badge-info">{{ $agents->count() }} agentes</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Agente</th>
                                <th>Ventanilla</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Total Atendidos</th>
                                <th class="text-center">Completados</th>
                                <th class="text-center">Ausentes</th>
                                <th class="text-center">T. Espera Prom.</th>
                                <th class="text-center">T. Atenci&oacute;n Prom.</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($agents as $agent)
                                <tr>
                                    <td>
                                        <i class="fas fa-user-circle mr-1 text-{{ $agent->is_online ? 'success' : 'secondary' }}"></i>
                                        {{ $agent->name }}
                                    </td>
                                    <td>{{ $agent->window }}</td>
                                    <td class="text-center">
                                        @if($agent->is_online)
                                            <span class="badge badge-success">En l&iacute;nea</span>
                                        @else
                                            <span class="badge badge-secondary">Desconectado</span>
                                        @endif
                                    </td>
                                    <td class="text-center"><strong>{{ $agent->total_attended }}</strong></td>
                                    <td class="text-center"><span class="text-success">{{ $agent->completed }}</span></td>
                                    <td class="text-center"><span class="text-danger">{{ $agent->absent }}</span></td>
                                    <td class="text-center">{{ $agent->avg_wait_time }}</td>
                                    <td class="text-center">{{ $agent->avg_service_time }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('supervisor.agent-detail', ['agent' => $agent->id, 'date' => $date->format('Y-m-d')]) }}"
                                           class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">No hay agentes registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Queues by Service Type -->
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-concierge-bell mr-2"></i>Por Tipo de Servicio
            </div>
            <div class="card-body">
                @forelse($queuesByService as $service)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="badge" style="background-color: {{ $service->color }}; color: #fff;">
                                {{ $service->prefix }}
                            </span>
                            {{ $service->name }}
                        </div>
                        <span class="badge badge-secondary">{{ $service->queues_count }}</span>
                    </div>
                @empty
                    <p class="text-muted text-center">No hay servicios configurados</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-history mr-2"></i>Actividad Reciente
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Turno</th>
                                <th>Servicio</th>
                                <th>Agente</th>
                                <th>Ventanilla</th>
                                <th>Estado</th>
                                <th>Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentQueues as $queue)
                                <tr>
                                    <td>
                                        <strong>{{ $queue->ticket_number }}</strong>
                                        @if($queue->priority !== 'normal')
                                            <span class="badge badge-{{ $queue->priority_color }}">{{ $queue->priority_label }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $queue->serviceType->color }}; color: #fff;">
                                            {{ $queue->serviceType->name }}
                                        </span>
                                    </td>
                                    <td>{{ $queue->agent ? $queue->agent->name : '-' }}</td>
                                    <td>{{ $queue->serviceWindow ? $queue->serviceWindow->name : '-' }}</td>
                                    <td><span class="badge badge-{{ $queue->status_color }}">{{ $queue->status_label }}</span></td>
                                    <td>{{ $queue->updated_at->format('H:i:s') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No hay actividad</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Hourly Chart
    const hourlyData = @json($hourlyData);
    const ctxHourly = document.getElementById('hourlyChart').getContext('2d');

    new Chart(ctxHourly, {
        type: 'bar',
        data: {
            labels: Object.keys(hourlyData).map(h => h + ':00'),
            datasets: [{
                label: 'Turnos',
                data: Object.values(hourlyData),
                backgroundColor: 'rgba(102, 126, 234, 0.8)',
                borderColor: 'rgba(102, 126, 234, 1)',
                borderWidth: 1,
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    // Status Pie Chart
    const statusData = @json($statusDistribution);
    const ctxStatus = document.getElementById('statusChart').getContext('2d');

    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: Object.keys(statusData),
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: [
                    '#28a745', // Completados
                    '#ffc107', // En espera
                    '#17a2b8', // En atencion
                    '#dc3545', // Ausentes
                    '#6c757d', // Cancelados
                ],
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15, font: { size: 12 } }
                }
            }
        }
    });

    @if($date->isToday())
    // Auto refresh every 30 seconds only for today
    setTimeout(function() { location.reload(); }, 30000);
    @endif
</script>
@endpush
