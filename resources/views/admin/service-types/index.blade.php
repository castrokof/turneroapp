@extends('layouts.app')

@section('title', 'Tipos de Servicio - Sistema de Turnos')
@section('page-title', 'Tipos de Servicio')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-concierge-bell mr-2"></i>Lista de Servicios</span>
        <a href="{{ route('admin.service-types.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i>Nuevo Servicio
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="60">Color</th>
                        <th>Prefijo</th>
                        <th>Nombre</th>
                        <th>Tiempo Est.</th>
                        <th>Límite Diario</th>
                        <th>Turnos Hoy</th>
                        <th>Estado</th>
                        <th width="150">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($serviceTypes as $service)
                        <tr>
                            <td>
                                <div style="width: 30px; height: 30px; background-color: {{ $service->color }}; border-radius: 5px;"></div>
                            </td>
                            <td><strong>{{ $service->prefix }}</strong></td>
                            <td>{{ $service->name }}</td>
                            <td>{{ $service->estimated_time }} min</td>
                            <td>{{ $service->daily_limit ?? 'Sin límite' }}</td>
                            <td>
                                <span class="badge badge-primary">{{ $service->queues_count }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $service->is_active ? 'success' : 'secondary' }}">
                                    {{ $service->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                                @if($service->requires_appointment)
                                    <span class="badge badge-info">Cita</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.service-types.edit', $service) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-{{ $service->is_active ? 'warning' : 'success' }}"
                                        onclick="toggleStatus({{ $service->id }})" title="{{ $service->is_active ? 'Desactivar' : 'Activar' }}">
                                    <i class="fas fa-{{ $service->is_active ? 'ban' : 'check' }}"></i>
                                </button>
                                <form action="{{ route('admin.service-types.destroy', $service) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Está seguro de eliminar este servicio?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No hay servicios configurados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleStatus(serviceId) {
    if (!confirm('¿Está seguro de cambiar el estado del servicio?')) return;

    $.post(`/admin/service-types/${serviceId}/toggle-status`)
        .done(function(response) {
            if (response.success) {
                location.reload();
            }
        })
        .fail(function(xhr) {
            alert(xhr.responseJSON?.message || 'Error al cambiar el estado');
        });
}
</script>
@endpush
