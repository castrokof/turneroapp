@extends('layouts.app')

@section('title', 'Clientes - Sistema de Turnos')
@section('page-title', 'Gestión de Clientes')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-user-friends mr-2"></i>Lista de Clientes</span>
        <a href="{{ route('admin.clients.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i>Nuevo Cliente
        </a>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form action="{{ route('admin.clients.index') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <input type="text" name="search" class="form-control"
                           placeholder="Buscar por documento, nombre, email o teléfono..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2 mb-2">
                    <div class="custom-control custom-checkbox mt-2">
                        <input type="checkbox" class="custom-control-input" id="priority" name="priority"
                               {{ request('priority') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="priority">Solo Prioritarios</label>
                    </div>
                </div>
                <div class="col-md-2 mb-2">
                    <button type="submit" class="btn btn-secondary btn-block">
                        <i class="fas fa-search mr-1"></i>Buscar
                    </button>
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Nombre</th>
                        <th>Contacto</th>
                        <th>Prioridad</th>
                        <th width="150">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr>
                            <td>
                                <strong>{{ strtoupper($client->document_type) }}</strong>: {{ $client->document_number }}
                            </td>
                            <td>{{ $client->full_name }}</td>
                            <td>
                                @if($client->email)
                                    <i class="fas fa-envelope text-muted mr-1"></i>{{ $client->email }}<br>
                                @endif
                                @if($client->phone)
                                    <i class="fas fa-phone text-muted mr-1"></i>{{ $client->phone }}
                                @endif
                            </td>
                            <td>
                                @if($client->is_elderly)
                                    <span class="badge badge-info"><i class="fas fa-user-clock"></i> Adulto Mayor</span>
                                @endif
                                @if($client->is_pregnant)
                                    <span class="badge badge-pink" style="background-color: #e83e8c; color: #fff;"><i class="fas fa-baby"></i> Embarazada</span>
                                @endif
                                @if($client->has_disability)
                                    <span class="badge badge-warning"><i class="fas fa-wheelchair"></i> Discapacidad</span>
                                @endif
                                @if(!$client->hasPriority())
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-sm btn-outline-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.clients.destroy', $client) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Está seguro de eliminar este cliente?')">
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
                            <td colspan="5" class="text-center text-muted">No se encontraron clientes</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $clients->withQueryString()->links() }}
    </div>
</div>
@endsection
