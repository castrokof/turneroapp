@extends('layouts.app')

@section('title', 'Configuración - Sistema de Turnos')
@section('page-title', 'Configuración del Sistema')

@section('content')
@if($settings->isEmpty())
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        No hay configuraciones definidas.
        <form action="{{ route('admin.settings.initialize') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-warning btn-sm ml-2">
                <i class="fas fa-cog mr-1"></i>Inicializar Configuración
            </button>
        </form>
    </div>
@else
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf

        @foreach($settings as $group => $groupSettings)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cog mr-2"></i>
                    {{ ucfirst($group) }}
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($groupSettings as $setting)
                            <div class="col-md-6 mb-3">
                                <label for="settings_{{ $setting->key }}">
                                    {{ $setting->description ?? $setting->key }}
                                </label>

                                @if($setting->type === 'boolean')
                                    <div class="custom-control custom-switch">
                                        <input type="hidden" name="settings[{{ $setting->key }}]" value="0">
                                        <input type="checkbox" class="custom-control-input"
                                               id="settings_{{ $setting->key }}"
                                               name="settings[{{ $setting->key }}]"
                                               value="1"
                                               {{ $setting->value ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="settings_{{ $setting->key }}">
                                            {{ $setting->value ? 'Habilitado' : 'Deshabilitado' }}
                                        </label>
                                    </div>
                                @elseif($setting->type === 'integer')
                                    <input type="number" class="form-control"
                                           id="settings_{{ $setting->key }}"
                                           name="settings[{{ $setting->key }}]"
                                           value="{{ $setting->value }}">
                                @elseif($setting->key === 'opening_time' || $setting->key === 'closing_time')
                                    <input type="time" class="form-control"
                                           id="settings_{{ $setting->key }}"
                                           name="settings[{{ $setting->key }}]"
                                           value="{{ $setting->value }}">
                                @elseif($setting->type === 'json')
                                    <textarea class="form-control"
                                              id="settings_{{ $setting->key }}"
                                              name="settings[{{ $setting->key }}]"
                                              rows="2">{{ is_array($setting->value) ? json_encode($setting->value) : $setting->value }}</textarea>
                                    <small class="form-text text-muted">Formato JSON</small>
                                @else
                                    <input type="text" class="form-control"
                                           id="settings_{{ $setting->key }}"
                                           name="settings[{{ $setting->key }}]"
                                           value="{{ $setting->value }}">
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        <div class="card">
            <div class="card-body">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i>Guardar Configuración
                </button>
                <form action="{{ route('admin.settings.initialize') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary ml-2">
                        <i class="fas fa-redo mr-1"></i>Restaurar Valores por Defecto
                    </button>
                </form>
            </div>
        </div>
    </form>
@endif
@endsection
