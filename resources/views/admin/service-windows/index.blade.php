@extends('layouts.app')

@section('title', 'Ventanillas - Sistema de Turnos')
@section('page-title', 'Gestión de Ventanillas')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-window-maximize mr-2"></i>Lista de Ventanillas</span>
        <a href="{{ route('admin.service-windows.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i>Nueva Ventanilla
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Servicios</th>
                        <th>Agente Actual</th>
                        <th>Estado</th>
                        <th>Atendidos Hoy</th>
                        <th width="150">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($windows as $window)
                        <tr>
                            <td><strong>{{ $window->code }}</strong></td>
                            <td>{{ $window->name }}</td>
                            <td>
                                @foreach($window->serviceTypes as $service)
                                    <span class="badge" style="background-color: {{ $service->color }}; color: #fff;">
                                        {{ $service->prefix }}
                                    </span>
                                @endforeach
                            </td>
                            <td>
                                @if($window->currentAgent)
                                    <i class="fas fa-user-circle text-success mr-1"></i>
                                    {{ $window->currentAgent->name }}
                                @else
                                    <span class="text-muted">Sin asignar</span>
                                @endif
                            </td>
                            <td>
                                @switch($window->status)
                                    @case('active')
                                        <span class="badge badge-success">Activa</span>
                                        @break
                                    @case('paused')
                                        <span class="badge badge-warning">Pausada</span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary">Inactiva</span>
                                @endswitch
                            </td>
                            <td>
                                <span class="badge badge-primary">{{ $window->queues_count }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.service-windows.edit', $window) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($window->status !== 'active')
                                    <form action="{{ route('admin.service-windows.destroy', $window) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Está seguro de eliminar esta ventanilla?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No hay ventanillas configuradas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
