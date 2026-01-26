

<?php $__env->startSection('title', 'Dashboard - Sistema de Turnos'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-4">
    <!-- Statistics Cards -->
    <div class="col-md-3 mb-3">
        <div class="card stat-card primary">
            <div class="card-body">
                <div class="stat-value text-primary"><?php echo e($stats['total_queues']); ?></div>
                <div class="stat-label">Turnos Hoy</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card warning">
            <div class="card-body">
                <div class="stat-value text-warning"><?php echo e($stats['pending_queues']); ?></div>
                <div class="stat-label">En Espera</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="stat-value text-success"><?php echo e($stats['completed']); ?></div>
                <div class="stat-label">Atendidos</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card danger">
            <div class="card-body">
                <div class="stat-value text-danger"><?php echo e($stats['absent']); ?></div>
                <div class="stat-label">Ausentes</div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card info">
            <div class="card-body">
                <div class="stat-value text-info"><?php echo e($stats['in_progress']); ?></div>
                <div class="stat-label">En Atención</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="stat-value text-success"><?php echo e($stats['active_windows']); ?></div>
                <div class="stat-label">Ventanillas Activas</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card primary">
            <div class="card-body">
                <div class="stat-value text-primary"><?php echo e($stats['avg_wait_time']); ?></div>
                <div class="stat-label">Tiempo Espera Promedio</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card info">
            <div class="card-body">
                <div class="stat-value text-info"><?php echo e($stats['avg_service_time']); ?></div>
                <div class="stat-label">Tiempo Atención Promedio</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Chart -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-chart-line mr-2"></i>Turnos por Hora</span>
                <span class="text-muted"><?php echo e(now()->format('d/m/Y')); ?></span>
            </div>
            <div class="card-body">
                <canvas id="hourlyChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Queues by Service -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-concierge-bell mr-2"></i>Por Tipo de Servicio
            </div>
            <div class="card-body">
                <?php $__empty_1 = true; $__currentLoopData = $queuesByService; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="badge" style="background-color: <?php echo e($service->color); ?>; color: #fff;">
                                <?php echo e($service->prefix); ?>

                            </span>
                            <?php echo e($service->name); ?>

                        </div>
                        <span class="badge badge-secondary"><?php echo e($service->queues_count); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted text-center">No hay servicios configurados</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Active Agents -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-headset mr-2"></i>Agentes Activos
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $activeAgents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-user-circle mr-2 text-success"></i>
                                <?php echo e($agent->name); ?>

                            </div>
                            <span class="badge badge-primary"><?php echo e($agent->serviceWindow->name ?? '-'); ?></span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="list-group-item text-muted text-center">No hay agentes activos</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Recent Queues -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-history mr-2"></i>Últimos Turnos</span>
                <a href="<?php echo e(route('admin.reports.index')); ?>" class="btn btn-sm btn-outline-primary">Ver Todos</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Turno</th>
                                <th>Cliente</th>
                                <th>Servicio</th>
                                <th>Estado</th>
                                <th>Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $recentQueues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $queue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($queue->ticket_number); ?></strong>
                                        <?php if($queue->priority !== 'normal'): ?>
                                            <span class="badge badge-<?php echo e($queue->priority_color); ?>"><?php echo e($queue->priority_label); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($queue->client ? $queue->client->full_name : 'Sin registrar'); ?></td>
                                    <td>
                                        <span class="badge" style="background-color: <?php echo e($queue->serviceType->color); ?>; color: #fff;">
                                            <?php echo e($queue->serviceType->name); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo e($queue->status_color); ?>"><?php echo e($queue->status_label); ?></span>
                                    </td>
                                    <td><?php echo e($queue->created_at->format('H:i')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No hay turnos hoy</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-bolt mr-2"></i>Acciones Rápidas
            </div>
            <div class="card-body">
                <a href="<?php echo e(route('display.kiosk')); ?>" class="btn btn-primary mr-2" target="_blank">
                    <i class="fas fa-plus mr-1"></i> Generar Turno
                </a>
                <a href="<?php echo e(route('display.tv')); ?>" class="btn btn-info mr-2" target="_blank">
                    <i class="fas fa-tv mr-1"></i> Pantalla TV
                </a>
                <a href="<?php echo e(route('agent.dashboard')); ?>" class="btn btn-success mr-2">
                    <i class="fas fa-headset mr-1"></i> Panel Agente
                </a>
                <a href="<?php echo e(route('admin.reports.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-file-alt mr-1"></i> Reportes
                </a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Hourly Chart
    const hourlyData = <?php echo json_encode($hourlyData, 15, 512) ?>;
    const ctx = document.getElementById('hourlyChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: Object.keys(hourlyData).map(h => h + ':00'),
            datasets: [{
                label: 'Turnos',
                data: Object.values(hourlyData),
                backgroundColor: 'rgba(102, 126, 234, 0.8)',
                borderColor: 'rgba(102, 126, 234, 1)',
                borderWidth: 1,
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Auto refresh every 30 seconds
    setTimeout(function() {
        location.reload();
    }, 30000);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\turnero_app\turneroapp\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>