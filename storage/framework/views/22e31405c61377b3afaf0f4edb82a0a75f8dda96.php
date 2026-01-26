

<?php $__env->startSection('title', 'Ventanillas - Sistema de Turnos'); ?>
<?php $__env->startSection('page-title', 'Gestión de Ventanillas'); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-window-maximize mr-2"></i>Lista de Ventanillas</span>
        <a href="<?php echo e(route('admin.service-windows.create')); ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i>Nueva Ventanilla
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Servicios</th>
                        <th>Agente Actual</th>
                        <th>Estado</th>
                        <th>Atendidos Hoy</th>
                        <th width="150">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $windows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $window): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($window->code); ?></strong></td>
                            <td><?php echo e($window->name); ?></td>
                            <td>
                                <?php $__currentLoopData = $window->serviceTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge" style="background-color: <?php echo e($service->color); ?>; color: #fff;">
                                        <?php echo e($service->prefix); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </td>
                            <td>
                                <?php if($window->currentAgent): ?>
                                    <i class="fas fa-user-circle text-success mr-1"></i>
                                    <?php echo e($window->currentAgent->name); ?>

                                <?php else: ?>
                                    <span class="text-muted">Sin asignar</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php switch($window->status):
                                    case ('active'): ?>
                                        <span class="badge badge-success">Activa</span>
                                        <?php break; ?>
                                    <?php case ('paused'): ?>
                                        <span class="badge badge-warning">Pausada</span>
                                        <?php break; ?>
                                    <?php default: ?>
                                        <span class="badge badge-secondary">Inactiva</span>
                                <?php endswitch; ?>
                            </td>
                            <td>
                                <span class="badge badge-primary"><?php echo e($window->queues_count); ?></span>
                            </td>
                            <td>
                                <a href="<?php echo e(route('admin.service-windows.edit', $window)); ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if($window->status !== 'active'): ?>
                                    <form action="<?php echo e(route('admin.service-windows.destroy', $window)); ?>" method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Está seguro de eliminar esta ventanilla?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No hay ventanillas configuradas</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\turnero_app\turneroapp\resources\views/admin/service-windows/index.blade.php ENDPATH**/ ?>