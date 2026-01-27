

<?php $__env->startSection('title', 'Reportes - Sistema de Turnos'); ?>
<?php $__env->startSection('page-title', 'Reportes y Estadísticas'); ?>

<?php $__env->startSection('content'); ?>
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-filter mr-2"></i>Filtros de Reporte
    </div>
    <div class="card-body">
        <form action="<?php echo e(route('admin.reports.generate')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="date_from">Desde</label>
                    <input type="date" name="date_from" id="date_from" class="form-control"
                           value="<?php echo e($validated['date_from'] ?? now()->startOfMonth()->format('Y-m-d')); ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="date_to">Hasta</label>
                    <input type="date" name="date_to" id="date_to" class="form-control"
                           value="<?php echo e($validated['date_to'] ?? now()->format('Y-m-d')); ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="service_type_id">Tipo de Servicio</label>
                    <select name="service_type_id" id="service_type_id" class="form-control">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $serviceTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($service->id); ?>" <?php echo e(($validated['service_type_id'] ?? '') == $service->id ? 'selected' : ''); ?>>
                                <?php echo e($service->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="agent_id">Agente</label>
                    <select name="agent_id" id="agent_id" class="form-control">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($agent->id); ?>" <?php echo e(($validated['agent_id'] ?? '') == $agent->id ? 'selected' : ''); ?>>
                                <?php echo e($agent->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="service_window_id">Ventanilla</label>
                    <select name="service_window_id" id="service_window_id" class="form-control">
                        <option value="">Todas</option>
                        <?php $__currentLoopData = $windows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $window): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($window->id); ?>" <?php echo e(($validated['service_window_id'] ?? '') == $window->id ? 'selected' : ''); ?>>
                                <?php echo e($window->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="status">Estado</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">Todos</option>
                        <option value="completed" <?php echo e(($validated['status'] ?? '') === 'completed' ? 'selected' : ''); ?>>Completado</option>
                        <option value="absent" <?php echo e(($validated['status'] ?? '') === 'absent' ? 'selected' : ''); ?>>Ausente</option>
                        <option value="cancelled" <?php echo e(($validated['status'] ?? '') === 'cancelled' ? 'selected' : ''); ?>>Cancelado</option>
                        <option value="pending" <?php echo e(($validated['status'] ?? '') === 'pending' ? 'selected' : ''); ?>>Pendiente</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search mr-1"></i>Generar Reporte
                    </button>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <a href="<?php echo e(route('admin.reports.export', request()->query())); ?>" class="btn btn-success btn-block">
                        <i class="fas fa-file-excel mr-1"></i>Exportar CSV
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if(isset($stats)): ?>
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-primary"><?php echo e($stats['total']); ?></h3>
                <small class="text-muted">Total Turnos</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-success"><?php echo e($stats['completed']); ?></h3>
                <small class="text-muted">Completados</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-danger"><?php echo e($stats['absent']); ?></h3>
                <small class="text-muted">Ausentes</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-info"><?php echo e($stats['completion_rate']); ?>%</h3>
                <small class="text-muted">Tasa Completados</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-warning"><?php echo e($stats['avg_wait_time']); ?></h3>
                <small class="text-muted">Espera Prom.</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-secondary"><?php echo e($stats['avg_service_time']); ?></h3>
                <small class="text-muted">Atención Prom.</small>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Turnos por Servicio</div>
            <div class="card-body">
                <canvas id="chartByService" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Turnos por Hora</div>
            <div class="card-body">
                <canvas id="chartByHour" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-table mr-2"></i>Detalle de Turnos (<?php echo e($queues->count()); ?> registros)
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
            <table class="table table-hover table-sm mb-0">
                <thead class="thead-light" style="position: sticky; top: 0;">
                    <tr>
                        <th>Fecha</th>
                        <th>Turno</th>
                        <th>Cliente</th>
                        <th>Servicio</th>
                        <th>Ventanilla</th>
                        <th>Agente</th>
                        <th>Estado</th>
                        <th>Espera</th>
                        <th>Atención</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $queues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $queue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($queue->queue_date->format('d/m')); ?></td>
                            <td><strong><?php echo e($queue->ticket_number); ?></strong></td>
                            <td><?php echo e($queue->client ? $queue->client->full_name : '-'); ?></td>
                            <td>
                                <span class="badge" style="background-color: <?php echo e($queue->serviceType->color); ?>; color: #fff;">
                                    <?php echo e($queue->serviceType->prefix); ?>

                                </span>
                            </td>
                            <td><?php echo e($queue->serviceWindow->name ?? '-'); ?></td>
                            <td><?php echo e($queue->agent->name ?? '-'); ?></td>
                            <td><span class="badge badge-<?php echo e($queue->status_color); ?>"><?php echo e($queue->status_label); ?></span></td>
                            <td><?php echo e($queue->getWaitTimeFormatted()); ?></td>
                            <td><?php echo e($queue->getServiceTimeFormatted()); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">No se encontraron turnos</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php if(isset($stats)): ?>
<?php $__env->startPush('scripts'); ?>
<script>
    // Chart by Service
    $.get('<?php echo e(route("admin.reports.chart")); ?>', {
        date_from: '<?php echo e($validated["date_from"]); ?>',
        date_to: '<?php echo e($validated["date_to"]); ?>',
        type: 'by_service'
    }, function(data) {
        new Chart(document.getElementById('chartByService'), {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
                    backgroundColor: data.colors
                }]
            }
        });
    });

    // Chart by Hour
    $.get('<?php echo e(route("admin.reports.chart")); ?>', {
        date_from: '<?php echo e($validated["date_from"]); ?>',
        date_to: '<?php echo e($validated["date_to"]); ?>',
        type: 'by_hour'
    }, function(data) {
        new Chart(document.getElementById('chartByHour'), {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Turnos',
                    data: data.data,
                    backgroundColor: 'rgba(102, 126, 234, 0.8)'
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\turneroapp\resources\views/admin/reports/index.blade.php ENDPATH**/ ?>