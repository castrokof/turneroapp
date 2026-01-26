@extends('layouts.app')

@section('title', 'Panel de Agente - Sistema de Turnos')
@section('page-title', 'Panel de Atención')

@push('styles')
<style>
    .current-ticket {
        font-size: 4rem;
        font-weight: bold;
        line-height: 1.2;
    }
    .queue-item {
        cursor: pointer;
        transition: all 0.2s;
    }
    .queue-item:hover {
        background-color: #f8f9fa;
    }
    .timer {
        font-size: 2rem;
        font-family: monospace;
    }
    .action-btn {
        padding: 1rem 2rem;
        font-size: 1.1rem;
    }
</style>
@endpush

@section('content')
<div class="row">
    <!-- Current Queue Panel -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span><i class="fas fa-headset mr-2"></i>{{ $window->name }} - {{ $window->code }}</span>
                <div>
                    @if($window->status === 'active')
                        <span class="badge badge-success">Activa</span>
                    @elseif($window->status === 'paused')
                        <span class="badge badge-warning">Pausada</span>
                    @endif
                </div>
            </div>
            <div class="card-body text-center">
                @if($currentQueue)
                    <div class="mb-3">
                        <span class="badge badge-{{ $currentQueue->status_color }} mb-2" style="font-size: 1rem;">
                            {{ $currentQueue->status_label }}
                        </span>
                        <div class="current-ticket" style="color: {{ $currentQueue->serviceType->color }}">
                            {{ $currentQueue->ticket_number }}
                        </div>
                        <p class="text-muted mb-1">{{ $currentQueue->serviceType->name }}</p>
                        @if($currentQueue->client)
                            <p class="mb-0"><strong>{{ $currentQueue->client->full_name }}</strong></p>
                            <small class="text-muted">{{ $currentQueue->client->document_display }}</small>
                        @else
                            <p class="text-muted">Cliente sin registrar</p>
                        @endif
                        @if($currentQueue->priority !== 'normal')
                            <span class="badge badge-{{ $currentQueue->priority_color }} mt-2">
                                {{ $currentQueue->priority_label }}
                            </span>
                        @endif
                    </div>

                    <div class="timer mb-4" id="serviceTimer">00:00</div>

                    <div class="row">
                        @if($currentQueue->status === 'called')
                            <div class="col-6">
                                <button class="btn btn-warning btn-block action-btn" onclick="recallTurn()">
                                    <i class="fas fa-redo mr-2"></i>Rellamar
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-success btn-block action-btn" onclick="startService()">
                                    <i class="fas fa-play mr-2"></i>Iniciar
                                </button>
                            </div>
                        @elseif($currentQueue->status === 'in_progress')
                            <div class="col-12 mb-2">
                                <button class="btn btn-success btn-block action-btn" onclick="completeService()">
                                    <i class="fas fa-check mr-2"></i>Finalizar Atención
                                </button>
                            </div>
                        @endif
                        <div class="col-6 mt-2">
                            <button class="btn btn-outline-secondary btn-block" onclick="markAbsent()">
                                <i class="fas fa-user-slash mr-1"></i>Ausente
                            </button>
                        </div>
                        <div class="col-6 mt-2">
                            <button class="btn btn-outline-info btn-block" data-toggle="modal" data-target="#transferModal">
                                <i class="fas fa-exchange-alt mr-1"></i>Transferir
                            </button>
                        </div>
                    </div>
                @else
                    <div class="py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">Sin turno en atención</h4>
                        <button class="btn btn-primary btn-lg mt-3" onclick="callNext()">
                            <i class="fas fa-bullhorn mr-2"></i>Llamar Siguiente Turno
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Today Stats -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-bar mr-2"></i>Mi Estadística de Hoy
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <h3 class="text-success">{{ $todayStats['completed'] }}</h3>
                        <small class="text-muted">Atendidos</small>
                    </div>
                    <div class="col-4">
                        <h3 class="text-danger">{{ $todayStats['absent'] }}</h3>
                        <small class="text-muted">Ausentes</small>
                    </div>
                    <div class="col-4">
                        <h3 class="text-info">{{ $todayStats['avg_time'] ? gmdate('i:s', $todayStats['avg_time']) : '00:00' }}</h3>
                        <small class="text-muted">T. Promedio</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Queue -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-list mr-2"></i>Turnos Pendientes</span>
                <span class="badge badge-primary" id="pendingCount">{{ $pendingQueues->count() }}</span>
            </div>
            <div class="card-body p-0" style="max-height: 500px; overflow-y: auto;">
                <ul class="list-group list-group-flush" id="pendingList">
                    @forelse($pendingQueues as $queue)
                        <li class="list-group-item queue-item d-flex justify-content-between align-items-center"
                            onclick="callSpecific({{ $queue->id }})">
                            <div>
                                <strong style="color: {{ $queue->serviceType->color }}">{{ $queue->ticket_number }}</strong>
                                <small class="text-muted ml-2">{{ $queue->serviceType->prefix }}</small>
                                @if($queue->priority !== 'normal')
                                    <span class="badge badge-{{ $queue->priority_color }} ml-2">{{ $queue->priority_label }}</span>
                                @endif
                                @if($queue->client)
                                    <br><small class="text-muted">{{ $queue->client->full_name }}</small>
                                @endif
                            </div>
                            <div class="text-right">
                                <small class="text-muted">{{ $queue->created_at->format('H:i') }}</small>
                                <br>
                                <small class="text-muted">{{ $queue->created_at->diffForHumans(null, true) }}</small>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted py-5">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <br>No hay turnos pendientes
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <i class="fas fa-bolt mr-2"></i>Acciones
            </div>
            <div class="card-body">
                @if($window->status === 'active')
                    <button class="btn btn-warning mr-2" onclick="pauseWindow()">
                        <i class="fas fa-pause mr-1"></i>Pausar Ventanilla
                    </button>
                @elseif($window->status === 'paused')
                    <button class="btn btn-success mr-2" onclick="resumeWindow()">
                        <i class="fas fa-play mr-1"></i>Reanudar Ventanilla
                    </button>
                @endif
                <form action="{{ route('agent.leave-window') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('¿Está seguro de abandonar la ventanilla?')">
                        <i class="fas fa-sign-out-alt mr-1"></i>Abandonar Ventanilla
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transferir Turno</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Ventanilla de Destino</label>
                    <select id="transferWindowId" class="form-control">
                        @foreach(\App\Models\ServiceWindow::where('id', '!=', $window->id)->available()->get() as $w)
                            <option value="{{ $w->id }}">{{ $w->name }} ({{ $w->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Razón (opcional)</label>
                    <textarea id="transferReason" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="transferQueue()">Transferir</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let timerInterval = null;
    let startTime = @json($currentQueue && $currentQueue->started_at ? $currentQueue->started_at->timestamp : null);

    function updateTimer() {
        if (!startTime) return;

        const now = Math.floor(Date.now() / 1000);
        const elapsed = now - startTime;
        const minutes = Math.floor(elapsed / 60).toString().padStart(2, '0');
        const seconds = (elapsed % 60).toString().padStart(2, '0');
        $('#serviceTimer').text(`${minutes}:${seconds}`);
    }

    if (startTime) {
        updateTimer();
        timerInterval = setInterval(updateTimer, 1000);
    }

    function callNext() {
        $.post('{{ route("agent.call-next") }}')
            .done(function(response) {
                if (response.success) {
                    location.reload();
                }
            })
            .fail(function(xhr) {
                alert(xhr.responseJSON?.message || 'Error al llamar turno');
            });
    }

    function callSpecific(queueId) {
        if (!confirm('¿Llamar este turno específico?')) return;

        $.post(`/agent/call/${queueId}`)
            .done(function(response) {
                if (response.success) {
                    location.reload();
                }
            })
            .fail(function(xhr) {
                alert(xhr.responseJSON?.message || 'Error al llamar turno');
            });
    }

    function recallTurn() {
        $.post('{{ route("agent.recall") }}')
            .done(function(response) {
                alert('Turno rellamado');
            })
            .fail(function(xhr) {
                alert(xhr.responseJSON?.message || 'Error');
            });
    }

    function startService() {
        $.post('{{ route("agent.start-service") }}')
            .done(function(response) {
                if (response.success) {
                    location.reload();
                }
            })
            .fail(function(xhr) {
                alert(xhr.responseJSON?.message || 'Error');
            });
    }

    function completeService() {
        const notes = prompt('Notas adicionales (opcional):');

        $.post('{{ route("agent.complete-service") }}', { notes: notes })
            .done(function(response) {
                if (response.success) {
                    location.reload();
                }
            })
            .fail(function(xhr) {
                alert(xhr.responseJSON?.message || 'Error');
            });
    }

    function markAbsent() {
        if (!confirm('¿Marcar este turno como ausente?')) return;

        $.post('{{ route("agent.mark-absent") }}')
            .done(function(response) {
                if (response.success) {
                    location.reload();
                }
            })
            .fail(function(xhr) {
                alert(xhr.responseJSON?.message || 'Error');
            });
    }

    function transferQueue() {
        const windowId = $('#transferWindowId').val();
        const reason = $('#transferReason').val();

        $.post('{{ route("agent.transfer") }}', { window_id: windowId, reason: reason })
            .done(function(response) {
                if (response.success) {
                    location.reload();
                }
            })
            .fail(function(xhr) {
                alert(xhr.responseJSON?.message || 'Error al transferir');
            });
    }

    function pauseWindow() {
        $.post('{{ route("agent.pause-window") }}')
            .done(function(response) {
                if (response.success) {
                    location.reload();
                }
            })
            .fail(function(xhr) {
                alert(xhr.responseJSON?.message || 'Error');
            });
    }

    function resumeWindow() {
        $.post('{{ route("agent.resume-window") }}')
            .done(function(response) {
                if (response.success) {
                    location.reload();
                }
            })
            .fail(function(xhr) {
                alert(xhr.responseJSON?.message || 'Error');
            });
    }

    // Auto-refresh pending list every 10 seconds
    setInterval(function() {
        $.get('{{ route("agent.pending-queues") }}')
            .done(function(response) {
                $('#pendingCount').text(response.count);
            });
    }, 10000);
</script>
@endpush
