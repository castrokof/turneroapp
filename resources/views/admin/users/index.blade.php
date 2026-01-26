@extends('layouts.app')

@section('title', 'Usuarios - Sistema de Turnos')
@section('page-title', 'Gestión de Usuarios')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-users mr-2"></i>Lista de Usuarios</span>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i>Nuevo Usuario
        </a>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form action="{{ route('admin.users.index') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o email..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2 mb-2">
                    <select name="role" class="form-control">
                        <option value="">Todos los roles</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrador</option>
                        <option value="agent" {{ request('role') === 'agent' ? 'selected' : '' }}>Agente</option>
                        <option value="viewer" {{ request('role') === 'viewer' ? 'selected' : '' }}>Visualizador</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <select name="status" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activo</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <button type="submit" class="btn btn-secondary btn-block">
                        <i class="fas fa-search mr-1"></i>Filtrar
                    </button>
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Último Acceso</th>
                        <th width="150">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <i class="fas fa-user-circle mr-2 text-{{ $user->is_active ? 'success' : 'secondary' }}"></i>
                                {{ $user->name }}
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @switch($user->role)
                                    @case('admin')
                                        <span class="badge badge-danger">Administrador</span>
                                        @break
                                    @case('agent')
                                        <span class="badge badge-primary">Agente</span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary">Visualizador</span>
                                @endswitch
                            </td>
                            <td>
                                <span class="badge badge-{{ $user->is_active ? 'success' : 'secondary' }}">
                                    {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Nunca' }}
                            </td>
                            <td>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                    <button type="button" class="btn btn-sm btn-outline-{{ $user->is_active ? 'warning' : 'success' }}"
                                            onclick="toggleStatus({{ $user->id }})" title="{{ $user->is_active ? 'Desactivar' : 'Activar' }}">
                                        <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                                    </button>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Está seguro de eliminar este usuario?')">
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
                            <td colspan="6" class="text-center text-muted">No se encontraron usuarios</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $users->withQueryString()->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleStatus(userId) {
    if (!confirm('¿Está seguro de cambiar el estado del usuario?')) return;

    $.post(`/admin/users/${userId}/toggle-status`)
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
