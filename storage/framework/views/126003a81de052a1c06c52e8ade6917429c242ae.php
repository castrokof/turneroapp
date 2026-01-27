

<?php $__env->startSection('title', 'Seleccionar Ventanilla - Sistema de Turnos'); ?>
<?php $__env->startSection('page-title', 'Seleccionar Ventanilla'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-window-maximize mr-2"></i>Seleccione una Ventanilla para Comenzar
            </div>
            <div class="card-body">
                <?php if($windows->isEmpty()): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        No hay ventanillas disponibles. Contacte al administrador.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php $__currentLoopData = $windows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $window): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 <?php echo e($window->status === 'active' ? 'border-success' : ''); ?>">
                                    <div class="card-body text-center">
                                        <h4><?php echo e($window->name); ?></h4>
                                        <h2 class="text-primary"><?php echo e($window->code); ?></h2>

                                        <?php if($window->location): ?>
                                            <p class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i><?php echo e($window->location); ?></p>
                                        <?php endif; ?>

                                        <div class="mb-3">
                                            <?php $__currentLoopData = $window->serviceTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="badge" style="background-color: <?php echo e($service->color); ?>; color: #fff;">
                                                    <?php echo e($service->prefix); ?>

                                                </span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>

                                        <?php if($window->status === 'active' && $window->currentAgent): ?>
                                            <div class="alert alert-info mb-3">
                                                <small>
                                                    <i class="fas fa-user mr-1"></i>
                                                    En uso por: <?php echo e($window->currentAgent->name); ?>

                                                </small>
                                            </div>
                                        <?php endif; ?>

                                        <form action="<?php echo e(route('agent.select-window')); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="window_id" value="<?php echo e($window->id); ?>">
                                            <button type="submit" class="btn btn-primary btn-block"
                                                    <?php echo e($window->status === 'active' && $window->current_agent_id !== auth()->id() ? 'disabled' : ''); ?>>
                                                <i class="fas fa-check mr-1"></i>Seleccionar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\turneroapp\resources\views/agent/select-window.blade.php ENDPATH**/ ?>