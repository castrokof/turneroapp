@extends('layouts.app')

@section('title', 'Nueva Ventanilla - Sistema de Turnos')
@section('page-title', 'Nueva Ventanilla')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-plus mr-2"></i>Crear Nueva Ventanilla
            </div>
            <div class="card-body">
                <form action="{{ route('admin.service-windows.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="name">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}" required placeholder="Ej: Ventanilla 1">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="code">Código <span class="text-danger">*</span></label>
                                <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror"
                                       value="{{ old('code') }}" required maxlength="10" placeholder="Ej: V1" style="text-transform: uppercase;">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Descripción</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="2" placeholder="Descripción opcional...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="location">Ubicación</label>
                                <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror"
                                       value="{{ old('location') }}" placeholder="Ej: Planta Baja, Sector A">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="display_order">Orden</label>
                                <input type="number" name="display_order" id="display_order"
                                       class="form-control @error('display_order') is-invalid @enderror"
                                       value="{{ old('display_order', 0) }}" min="0">
                                @error('display_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Servicios que Atiende</label>
                        <div class="row">
                            @foreach($serviceTypes as $service)
                                <div class="col-md-6">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input"
                                               id="service_{{ $service->id }}"
                                               name="service_types[]"
                                               value="{{ $service->id }}"
                                               {{ in_array($service->id, old('service_types', [])) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="service_{{ $service->id }}">
                                            <span class="badge" style="background-color: {{ $service->color }}; color: #fff;">{{ $service->prefix }}</span>
                                            {{ $service->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($serviceTypes->isEmpty())
                            <small class="text-muted">No hay servicios configurados. <a href="{{ route('admin.service-types.create') }}">Crear servicio</a></small>
                        @endif
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.service-windows.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>Guardar Ventanilla
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
