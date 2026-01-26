@extends('layouts.app')

@section('title', 'Dashboard - Sistema de Turnos')
@section('page-title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <!-- Statistics Cards -->
    <div class="col-md-3 mb-3">
        <div class="card stat-card primary">
            <div class="card-body">
                <div class="stat-value text-primary">{{ $stats['total_queues'] }}</div>
                <div class="stat-label">Turnos Hoy</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card warning">
            <div class="card-body">
                <div class="stat-value text-warning">{{ $stats['pending_queues'] }}</div>
                <div class="stat-label">En Espera</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="stat-value text-success">{{ $stats['completed'] }}</div>
                <div class="stat-label">Atendidos</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card danger">
            <div class="card-body">
                <div class="stat-value text-danger">{{ $stats['absent'] }}</div>
                <div class="stat-label">Ausentes</div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card info">
            <div class="card-body">
                <div class="stat-value text-info">{{ $stats['in_progress'] }}</div>
                <div class="stat-label">En Atención</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="stat-value text-success">{{ $stats['active_windows'] }}</div>
                <div class="stat-label">Ventanillas Activas</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card primary">
            <div class="card-body">
                <div class="stat-value text-primary">{{ $stats['avg_wait_time'] }}</div>
                <div class="stat-label">Tiempo Espera Promedio</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card info">
            <div class="card-body">
                <div class="stat-value text-info">{{ $stats['avg_service_time'] }}</div>
                <div class="stat-label">Tiempo Atención Promedio</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Chart -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-chart-line mr-2"></i>Turnos por Hora</span>
                <span class="text-muted">{{ now()->format('d/m/Y') }}</span>
            </div>
            <div class="card-body">
                <canvas id="hourlyChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Queues by Service -->
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
</div>

<div class="row">
    <!-- Active Agents -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-headset mr-2"></i>Agentes Activos
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($activeAgents as $agent)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-user-circle mr-2 text-success"></i>
                                {{ $agent->name }}
                            </div>
                            <span class="badge badge-primary">{{ $agent->serviceWindow->name ?? '-' }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center">No hay agentes activos</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Recent Queues -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-history mr-2"></i>Últimos Turnos</span>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-primary">Ver Todos</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Turno</th>
                                <th>Cliente</th>
                                <th>Servicio</th>
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
                                    <td>{{ $queue->client ? $queue->client->full_name : 'Sin registrar' }}</td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $queue->serviceType->color }}; color: #fff;">
                                            {{ $queue->serviceType->name }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $queue->status_color }}">{{ $queue->status_label }}</span>
                                    </td>
                                    <td>{{ $queue->created_at->format('H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No hay turnos hoy</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-bolt mr-2"></i>Acciones Rápidas
            </div>
            <div class="card-body">
                <a href="{{ route('display.kiosk') }}" class="btn btn-primary mr-2" target="_blank">
                    <i class="fas fa-plus mr-1"></i> Generar Turno
                </a>
                <a href="{{ route('display.tv') }}" class="btn btn-info mr-2" target="_blank">
                    <i class="fas fa-tv mr-1"></i> Pantalla TV
                </a>
                <a href="{{ route('agent.dashboard') }}" class="btn btn-success mr-2">
                    <i class="fas fa-headset mr-1"></i> Panel Agente
                </a>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-file-alt mr-1"></i> Reportes
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Hourly Chart
    const hourlyData = @json($hourlyData);
    const ctx = document.getElementById('hourlyChart').getContext('2d');

    new Chart(ctx, {
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
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Auto refresh every 30 seconds
    setTimeout(function() {
        location.reload();
    }, 30000);
</script>
@endpush
