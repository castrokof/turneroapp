@extends('layouts.app')

@section('title', 'Nuevo Servicio - Sistema de Turnos')
@section('page-title', 'Nuevo Tipo de Servicio')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-plus mr-2"></i>Crear Tipo de Servicio
            </div>
            <div class="card-body">
                <form action="{{ route('admin.service-types.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="name">Nombre del Servicio <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}" required placeholder="Ej: Consulta General">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="prefix">Prefijo <span class="text-danger">*</span></label>
                                <input type="text" name="prefix" id="prefix" class="form-control @error('prefix') is-invalid @enderror"
                                       value="{{ old('prefix') }}" required maxlength="5" placeholder="Ej: CG" style="text-transform: uppercase;">
                                @error('prefix')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Máximo 5 caracteres</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="color">Color <span class="text-danger">*</span></label>
                                <input type="color" name="color" id="color" class="form-control @error('color') is-invalid @enderror"
                                       value="{{ old('color', '#007bff') }}" required style="height: 40px;">
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="estimated_time">Tiempo Estimado (min) <span class="text-danger">*</span></label>
                                <input type="number" name="estimated_time" id="estimated_time"
                                       class="form-control @error('estimated_time') is-invalid @enderror"
                                       value="{{ old('estimated_time', 15) }}" required min="1" max="480">
                                @error('estimated_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="daily_limit">Límite Diario</label>
                                <input type="number" name="daily_limit" id="daily_limit"
                                       class="form-control @error('daily_limit') is-invalid @enderror"
                                       value="{{ old('daily_limit') }}" min="1" placeholder="Sin límite">
                                @error('daily_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Descripción</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3" placeholder="Descripción opcional del servicio...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="display_order">Orden de Visualización</label>
                        <input type="number" name="display_order" id="display_order"
                               class="form-control @error('display_order') is-invalid @enderror"
                               value="{{ old('display_order', 0) }}" min="0">
                        @error('display_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Los servicios se ordenan de menor a mayor</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" checked>
                                    <label class="custom-control-label" for="is_active">Servicio Activo</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="requires_appointment" name="requires_appointment">
                                    <label class="custom-control-label" for="requires_appointment">Requiere Cita Previa</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.service-types.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>Guardar Servicio
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
