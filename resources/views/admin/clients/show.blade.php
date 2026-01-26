@extends('layouts.app')

@section('title', 'Ver Cliente - Sistema de Turnos')
@section('page-title', 'Detalle de Cliente')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user mr-2"></i>Información del Cliente
            </div>
            <div class="card-body">
                <h4>{{ $client->full_name }}</h4>
                <p class="text-muted mb-3">{{ $client->document_display }}</p>

                @if($client->hasPriority())
                    <div class="mb-3">
                        @if($client->is_elderly)
                            <span class="badge badge-info"><i class="fas fa-user-clock"></i> Adulto Mayor</span>
                        @endif
                        @if($client->is_pregnant)
                            <span class="badge badge-pink" style="background-color: #e83e8c; color: #fff;"><i class="fas fa-baby"></i> Embarazada</span>
                        @endif
                        @if($client->has_disability)
                            <span class="badge badge-warning"><i class="fas fa-wheelchair"></i> Discapacidad</span>
                        @endif
                    </div>
                @endif

                <hr>

                <dl class="row mb-0">
                    @if($client->email)
                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8">{{ $client->email }}</dd>
                    @endif

                    @if($client->phone)
                        <dt class="col-sm-4">Teléfono:</dt>
                        <dd class="col-sm-8">{{ $client->phone }}</dd>
                    @endif

                    @if($client->birth_date)
                        <dt class="col-sm-4">Nacimiento:</dt>
                        <dd class="col-sm-8">{{ $client->birth_date->format('d/m/Y') }}</dd>
                    @endif

                    @if($client->gender)
                        <dt class="col-sm-4">Género:</dt>
                        <dd class="col-sm-8">
                            @switch($client->gender)
                                @case('male') Masculino @break
                                @case('female') Femenino @break
                                @default Otro
                            @endswitch
                        </dd>
                    @endif

                    @if($client->notes)
                        <dt class="col-sm-4">Notas:</dt>
                        <dd class="col-sm-8">{{ $client->notes }}</dd>
                    @endif

                    <dt class="col-sm-4">Registrado:</dt>
                    <dd class="col-sm-8">{{ $client->created_at->format('d/m/Y H:i') }}</dd>
                </dl>

                <hr>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Volver
                    </a>
                    <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit mr-1"></i>Editar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-history mr-2"></i>Historial de Turnos
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Turno</th>
                                <th>Servicio</th>
                                <th>Ventanilla</th>
                                <th>Agente</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $queue)
                                <tr>
                                    <td>{{ $queue->queue_date->format('d/m/Y') }}</td>
                                    <td><strong>{{ $queue->ticket_number }}</strong></td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $queue->serviceType->color }}; color: #fff;">
                                            {{ $queue->serviceType->name }}
                                        </span>
                                    </td>
                                    <td>{{ $queue->serviceWindow->name ?? '-' }}</td>
                                    <td>{{ $queue->agent->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $queue->status_color }}">{{ $queue->status_label }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No hay historial de turnos</td>
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
