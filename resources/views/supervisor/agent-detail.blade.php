@extends('layouts.app')

@section('title', 'Detalle Agente - Sistema de Turnos')
@section('page-title', 'Detalle de Agente')

@section('content')
<!-- Back button and date filter -->
<div class="row mb-3">
    <div class="col-md-6">
        <a href="{{ route('supervisor.dashboard', ['date' => $date->format('Y-m-d')]) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i>Volver al Dashboard
        </a>
    </div>
    <div class="col-md-6 text-right">
        <form action="{{ route('supervisor.agent-detail', $agent) }}" method="GET" class="form-inline justify-content-end">
            <label class="mr-2"><i class="fas fa-calendar-alt mr-1"></i>Fecha:</label>
            <input type="date" name="date" class="form-control form-control-sm mr-2" value="{{ $date->format('Y-m-d') }}">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
        </form>
    </div>
</div>

<!-- Agent Info -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-user mr-2"></i>{{ $agent->name }}
        <span class="text-muted ml-2">- {{ $date->format('d/m/Y') }}</span>
        @if($date->isToday())
            <span class="badge badge-success ml-1">Hoy</span>
        @endif
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-2 mb-3">
                <div class="card stat-card primary">
                    <div class="card-body text-center py-3">
                        <div class="stat-value text-primary" style="font-size: 1.8rem;">{{ $stats['total'] }}</div>
                        <div class="stat-label">Total</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <div class="card stat-card success">
                    <div class="card-body text-center py-3">
                        <div class="stat-value text-success" style="font-size: 1.8rem;">{{ $stats['completed'] }}</div>
                        <div class="stat-label">Completados</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <div class="card stat-card danger">
                    <div class="card-body text-center py-3">
                        <div class="stat-value text-danger" style="font-size: 1.8rem;">{{ $stats['absent'] }}</div>
                        <div class="stat-label">Ausentes</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <div class="card stat-card info">
                    <div class="card-body text-center py-3">
                        <div class="stat-value text-info" style="font-size: 1.8rem;">{{ $stats['in_progress'] }}</div>
                        <div class="stat-label">En Curso</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <div class="card stat-card primary">
                    <div class="card-body text-center py-3">
                        <div class="stat-value text-primary" style="font-size: 1.3rem;">{{ $stats['avg_wait_time'] }}</div>
                        <div class="stat-label">T. Espera</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <div class="card stat-card warning">
                    <div class="card-body text-center py-3">
                        <div class="stat-value text-warning" style="font-size: 1.3rem;">{{ $stats['avg_service_time'] }}</div>
                        <div class="stat-label">T. Atenci&oacute;n</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Queue List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-list mr-2"></i>Turnos Atendidos</span>
        <span class="badge badge-info">{{ $queues->count() }} turnos</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Turno</th>
                        <th>Cliente</th>
                        <th>Servicio</th>
                        <th>Prioridad</th>
                        <th>Ventanilla</th>
                        <th>Estado</th>
                        <th>Llamado</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>T. Espera</th>
                        <th>T. Atenci&oacute;n</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($queues as $queue)
                        <tr>
                            <td><strong>{{ $queue->ticket_number }}</strong></td>
                            <td>{{ $queue->client ? $queue->client->full_name : '-' }}</td>
                            <td>
                                <span class="badge" style="background-color: {{ $queue->serviceType->color }}; color: #fff;">
                                    {{ $queue->serviceType->name }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $queue->priority_color }}">{{ $queue->priority_label }}</span>
                            </td>
                            <td>{{ $queue->serviceWindow ? $queue->serviceWindow->name : '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $queue->status_color }}">{{ $queue->status_label }}</span>
                            </td>
                            <td>{{ $queue->called_at ? $queue->called_at->format('H:i:s') : '-' }}</td>
                            <td>{{ $queue->started_at ? $queue->started_at->format('H:i:s') : '-' }}</td>
                            <td>{{ $queue->completed_at ? $queue->completed_at->format('H:i:s') : '-' }}</td>
                            <td>{{ $queue->getWaitTimeFormatted() }}</td>
                            <td>{{ $queue->getServiceTimeFormatted() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted">No hay turnos para este agente en esta fecha</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
