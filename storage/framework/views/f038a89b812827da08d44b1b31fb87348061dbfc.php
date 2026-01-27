

<?php $__env->startSection('title', 'Panel de Agente - Sistema de Turnos'); ?>
<?php $__env->startSection('page-title', 'Panel de Atención'); ?>

<?php $__env->startPush('styles'); ?>
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
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <!-- Current Queue Panel -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span><i class="fas fa-headset mr-2"></i><?php echo e($window->name); ?> - <?php echo e($window->code); ?></span>
                <div>
                    <?php if($window->status === 'active'): ?>
                        <span class="badge badge-success">Activa</span>
                    <?php elseif($window->status === 'paused'): ?>
                        <span class="badge badge-warning">Pausada</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body text-center">
                <?php if($currentQueue): ?>
                    <div class="mb-3">
                        <span class="badge badge-<?php echo e($currentQueue->status_color); ?> mb-2" style="font-size: 1rem;">
                            <?php echo e($currentQueue->status_label); ?>

                        </span>
                        <div class="current-ticket" style="color: <?php echo e($currentQueue->serviceType->color); ?>">
                            <?php echo e($currentQueue->ticket_number); ?>

                        </div>
                        <p class="text-muted mb-1"><?php echo e($currentQueue->serviceType->name); ?></p>
                        <?php if($currentQueue->client): ?>
                            <p class="mb-0"><strong><?php echo e($currentQueue->client->full_name); ?></strong></p>
                            <small class="text-muted"><?php echo e($currentQueue->client->document_display); ?></small>
                        <?php else: ?>
                            <p class="text-muted">Cliente sin registrar</p>
                        <?php endif; ?>
                        <?php if($currentQueue->priority !== 'normal'): ?>
                            <span class="badge badge-<?php echo e($currentQueue->priority_color); ?> mt-2">
                                <?php echo e($currentQueue->priority_label); ?>

                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="timer mb-4" id="serviceTimer">00:00</div>

                    <div class="row">
                        <?php if($currentQueue->status === 'called'): ?>
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
                        <?php elseif($currentQueue->status === 'in_progress'): ?>
                            <div class="col-12 mb-2">
                                <button class="btn btn-success btn-block action-btn" onclick="completeService()">
                                    <i class="fas fa-check mr-2"></i>Finalizar Atención
                                </button>
                            </div>
                        <?php endif; ?>
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
                <?php else: ?>
                    <div class="py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">Sin turno en atención</h4>
                        <button class="btn btn-primary btn-lg mt-3" onclick="callNext()">
                            <i class="fas fa-bullhorn mr-2"></i>Llamar Siguiente Turno
                        </button>
                    </div>
                <?php endif; ?>
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
                        <h3 class="text-success"><?php echo e($todayStats['completed']); ?></h3>
                        <small class="text-muted">Atendidos</small>
                    </div>
                    <div class="col-4">
                        <h3 class="text-danger"><?php echo e($todayStats['absent']); ?></h3>
                        <small class="text-muted">Ausentes</small>
                    </div>
                    <div class="col-4">
                        <h3 class="text-info"><?php echo e($todayStats['avg_time'] ? gmdate('i:s', $todayStats['avg_time']) : '00:00'); ?></h3>
                        <small class="text-muted">T. Promedio</small>
                    </div>
                </div>
            </div>
        </div>
          <!-- Queue Summary by Service -->
        <div class="card mt-4">
            <div class="card-header">
                <i class="fas fa-layer-group mr-2"></i>Resumen de Turnos por Servicio
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush" id="queueSummaryList">
                    <?php $__empty_1 = true; $__currentLoopData = $queueSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $summary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge mr-2" style="background-color: <?php echo e($summary['color']); ?>; color: white;">
                                    <?php echo e($summary['prefix']); ?>

                                </span>
                                <?php echo e($summary['name']); ?>

                            </div>
                            <span class="badge badge-primary badge-pill"><?php echo e($summary['total']); ?></span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="list-group-item text-center text-muted">
                            <i class="fas fa-inbox mr-2"></i>Sin turnos pendientes
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Pending Queue -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span><i class="fas fa-list mr-2"></i>Turnos Pendientes</span>
                    <span class="badge badge-primary" id="pendingCount"><?php echo e($pendingQueues->count()); ?></span>
                </div>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" id="searchQueue" class="form-control" placeholder="Buscar turno (ej: A-001, nombre cliente...)">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0" style="max-height: 450px; overflow-y: auto;">
                <ul class="list-group list-group-flush" id="pendingList">
                    <?php $__empty_1 = true; $__currentLoopData = $pendingQueues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $queue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="list-group-item queue-item d-flex justify-content-between align-items-center"
                            onclick="callSpecific(<?php echo e($queue->id); ?>)">
                            <div>
                                <strong style="color: <?php echo e($queue->serviceType->color); ?>"><?php echo e($queue->ticket_number); ?></strong>
                                <small class="text-muted ml-2"><?php echo e($queue->serviceType->prefix); ?></small>
                                <?php if($queue->priority !== 'normal'): ?>
                                    <span class="badge badge-<?php echo e($queue->priority_color); ?> ml-2"><?php echo e($queue->priority_label); ?></span>
                                <?php endif; ?>
                                <?php if($queue->client): ?>
                                    <br><small class="text-muted"><?php echo e($queue->client->full_name); ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="text-right">
                                <small class="text-muted"><?php echo e($queue->created_at->format('H:i')); ?></small>
                                <br>
                                <small class="text-muted"><?php echo e($queue->created_at->diffForHumans(null, true)); ?></small>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="list-group-item text-center text-muted py-5">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <br>No hay turnos pendientes
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <i class="fas fa-bolt mr-2"></i>Acciones
            </div>
            <div class="card-body">
                <?php if($window->status === 'active'): ?>
                    <button class="btn btn-warning mr-2" onclick="pauseWindow()">
                        <i class="fas fa-pause mr-1"></i>Pausar Ventanilla
                    </button>
                <?php elseif($window->status === 'paused'): ?>
                    <button class="btn btn-success mr-2" onclick="resumeWindow()">
                        <i class="fas fa-play mr-1"></i>Reanudar Ventanilla
                    </button>
                <?php endif; ?>
                <form action="<?php echo e(route('agent.leave-window')); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
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
                        <?php $__currentLoopData = \App\Models\ServiceWindow::where('id', '!=', $window->id)->available()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($w->id); ?>"><?php echo e($w->name); ?> (<?php echo e($w->code); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    let timerInterval = null;
    let startTime = <?php echo json_encode($currentQueue && $currentQueue->started_at ? $currentQueue->started_at->timestamp : null, 15, 512) ?>;
    let allQueues = []; // Store all queues for filtering

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

    // Search functionality
    $('#searchQueue').on('input', function() {
        const searchTerm = $(this).val().toLowerCase().trim();
        filterQueues(searchTerm);
    });

    function clearSearch() {
        $('#searchQueue').val('');
        filterQueues('');
    }

    function filterQueues(searchTerm) {
        if (!searchTerm) {
            renderQueueList(allQueues);
            return;
        }

        const filtered = allQueues.filter(queue => {
            const ticketNumber = queue.ticket_number.toLowerCase();
            const clientName = queue.client
                ? `${queue.client.first_name} ${queue.client.last_name}`.toLowerCase()
                : '';
            const serviceName = queue.service_type.name.toLowerCase();

            return ticketNumber.includes(searchTerm) ||
                   clientName.includes(searchTerm) ||
                   serviceName.includes(searchTerm);
        });

        renderQueueList(filtered);
    }

    function callNext() {
        $.post('<?php echo e(route("agent.call-next")); ?>')
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
        $.post('<?php echo e(route("agent.recall")); ?>')
            .done(function(response) {
                alert('Turno rellamado');
            })
            .fail(function(xhr) {
                alert(xhr.responseJSON?.message || 'Error');
            });
    }

    function startService() {
        $.post('<?php echo e(route("agent.start-service")); ?>')
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

        $.post('<?php echo e(route("agent.complete-service")); ?>', { notes: notes })
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

        $.post('<?php echo e(route("agent.mark-absent")); ?>')
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

        $.post('<?php echo e(route("agent.transfer")); ?>', { window_id: windowId, reason: reason })
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
        $.post('<?php echo e(route("agent.pause-window")); ?>')
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
        $.post('<?php echo e(route("agent.resume-window")); ?>')
            .done(function(response) {
                if (response.success) {
                    location.reload();
                }
            })
            .fail(function(xhr) {
                alert(xhr.responseJSON?.message || 'Error');
            });
    }

    // Render queue list from array
    function renderQueueList(queues) {
        let html = '';
        if (queues.length === 0) {
            const searchTerm = $('#searchQueue').val();
            if (searchTerm) {
                html = `<li class="list-group-item text-center text-muted py-4">
                    <i class="fas fa-search fa-2x mb-2"></i>
                    <br>No se encontraron turnos con "${searchTerm}"
                </li>`;
            } else {
                html = `<li class="list-group-item text-center text-muted py-5">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <br>No hay turnos pendientes
                </li>`;
            }
        } else {
            queues.forEach(function(queue) {
                const priorityBadge = queue.priority !== 'normal'
                    ? `<span class="badge badge-${getPriorityColor(queue.priority)} ml-2">${getPriorityLabel(queue.priority)}</span>`
                    : '';
                const clientInfo = queue.client
                    ? `<br><small class="text-muted">${queue.client.first_name} ${queue.client.last_name}</small>`
                    : '';
                const createdAt = new Date(queue.created_at);
                const timeStr = createdAt.toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'});
                const diffMinutes = Math.floor((Date.now() - createdAt) / 60000);
                const diffStr = diffMinutes < 1 ? 'ahora' : `hace ${diffMinutes} min`;

                html += `<li class="list-group-item queue-item d-flex justify-content-between align-items-center"
                    onclick="callSpecific(${queue.id})">
                    <div>
                        <strong style="color: ${queue.service_type.color}">${queue.ticket_number}</strong>
                        <small class="text-muted ml-2">${queue.service_type.prefix}</small>
                        ${priorityBadge}
                        ${clientInfo}
                    </div>
                    <div class="text-right">
                        <small class="text-muted">${timeStr}</small>
                        <br>
                        <small class="text-muted">${diffStr}</small>
                    </div>
                </li>`;
            });
        }
        $('#pendingList').html(html);
    }

    // Auto-refresh pending list every 5 seconds
    function refreshPendingList() {
        $.get('<?php echo e(route("agent.pending-queues")); ?>')
            .done(function(response) {
                console.log('Total turnos pendientes:', response.count); // Debug log
                allQueues = response.queues;
                $('#pendingCount').text(response.count);

                // Apply current search filter
                const searchTerm = $('#searchQueue').val().toLowerCase().trim();
                if (searchTerm) {
                    filterQueues(searchTerm);
                } else {
                    renderQueueList(allQueues);
                }

                // Update queue summary by service
                if (response.summary) {
                    renderQueueSummary(response.summary);
                }
            });
    }

    // Render queue summary by service type
    function renderQueueSummary(summary) {
        let html = '';
        if (summary.length === 0) {
            html = `<li class="list-group-item text-center text-muted">
                <i class="fas fa-inbox mr-2"></i>Sin turnos pendientes
            </li>`;
        } else {
            summary.forEach(function(item) {
                html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge mr-2" style="background-color: ${item.color}; color: white;">
                            ${item.prefix}
                        </span>
                        ${item.name}
                    </div>
                    <span class="badge badge-primary badge-pill">${item.total}</span>
                </li>`;
            });
        }
        $('#queueSummaryList').html(html);
    }

    function getPriorityColor(priority) {
        const colors = {
            'emergency': 'danger',
            'priority': 'warning',
            'scheduled': 'info',
            'normal': 'secondary'
        };
        return colors[priority] || 'secondary';
    }

    function getPriorityLabel(priority) {
        const labels = {
            'emergency': 'Emergencia',
            'priority': 'Prioritario',
            'scheduled': 'Programado',
            'normal': 'Normal'
        };
        return labels[priority] || priority;
    }

    // Refresh every 5 seconds
    setInterval(refreshPendingList, 5000);

    // Refresh immediately on page load
    $(document).ready(function() {
        refreshPendingList();
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\turneroapp\resources\views/agent/dashboard.blade.php ENDPATH**/ ?>