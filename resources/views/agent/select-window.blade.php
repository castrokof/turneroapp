@extends('layouts.app')

@section('title', 'Seleccionar Ventanilla - Sistema de Turnos')
@section('page-title', 'Seleccionar Ventanilla')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-window-maximize mr-2"></i>Seleccione una Ventanilla para Comenzar
            </div>
            <div class="card-body">
                @if($windows->isEmpty())
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        No hay ventanillas disponibles. Contacte al administrador.
                    </div>
                @else
                    <div class="row">
                        @foreach($windows as $window)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 {{ $window->status === 'active' ? 'border-success' : '' }}">
                                    <div class="card-body text-center">
                                        <h4>{{ $window->name }}</h4>
                                        <h2 class="text-primary">{{ $window->code }}</h2>

                                        @if($window->location)
                                            <p class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i>{{ $window->location }}</p>
                                        @endif

                                        <div class="mb-3">
                                            @foreach($window->serviceTypes as $service)
                                                <span class="badge" style="background-color: {{ $service->color }}; color: #fff;">
                                                    {{ $service->prefix }}
                                                </span>
                                            @endforeach
                                        </div>

                                        @if($window->status === 'active' && $window->currentAgent)
                                            <div class="alert alert-info mb-3">
                                                <small>
                                                    <i class="fas fa-user mr-1"></i>
                                                    En uso por: {{ $window->currentAgent->name }}
                                                </small>
                                            </div>
                                        @endif

                                        <form action="{{ route('agent.select-window') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="window_id" value="{{ $window->id }}">
                                            <button type="submit" class="btn btn-primary btn-block"
                                                    {{ $window->status === 'active' && $window->current_agent_id !== auth()->id() ? 'disabled' : '' }}>
                                                <i class="fas fa-check mr-1"></i>Seleccionar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
