

<?php $__env->startSection('title', 'Tipos de Servicio - Sistema de Turnos'); ?>
<?php $__env->startSection('page-title', 'Tipos de Servicio'); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-concierge-bell mr-2"></i>Lista de Servicios</span>
        <a href="<?php echo e(route('admin.service-types.create')); ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i>Nuevo Servicio
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="60">Color</th>
                        <th>Prefijo</th>
                        <th>Nombre</th>
                        <th>Tiempo Est.</th>
                        <th>Límite Diario</th>
                        <th>Turnos Hoy</th>
                        <th>Estado</th>
                        <th width="150">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $serviceTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div style="width: 30px; height: 30px; background-color: <?php echo e($service->color); ?>; border-radius: 5px;"></div>
                            </td>
                            <td><strong><?php echo e($service->prefix); ?></strong></td>
                            <td><?php echo e($service->name); ?></td>
                            <td><?php echo e($service->estimated_time); ?> min</td>
                            <td><?php echo e($service->daily_limit ?? 'Sin límite'); ?></td>
                            <td>
                                <span class="badge badge-primary"><?php echo e($service->queues_count); ?></span>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo e($service->is_active ? 'success' : 'secondary'); ?>">
                                    <?php echo e($service->is_active ? 'Activo' : 'Inactivo'); ?>

                                </span>
                                <?php if($service->requires_appointment): ?>
                                    <span class="badge badge-info">Cita</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('admin.service-types.edit', $service)); ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-<?php echo e($service->is_active ? 'warning' : 'success'); ?>"
                                        onclick="toggleStatus(<?php echo e($service->id); ?>)" title="<?php echo e($service->is_active ? 'Desactivar' : 'Activar'); ?>">
                                    <i class="fas fa-<?php echo e($service->is_active ? 'ban' : 'check'); ?>"></i>
                                </button>
                                <form action="<?php echo e(route('admin.service-types.destroy', $service)); ?>" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Está seguro de eliminar este servicio?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">No hay servicios configurados</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function toggleStatus(serviceId) {
    if (!confirm('¿Está seguro de cambiar el estado del servicio?')) return;

    $.post(`/admin/service-types/${serviceId}/toggle-status`)
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\turneroapp\resources\views/admin/service-types/index.blade.php ENDPATH**/ ?>