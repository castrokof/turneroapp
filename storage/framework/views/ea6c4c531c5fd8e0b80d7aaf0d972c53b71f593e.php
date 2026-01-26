

<?php $__env->startSection('title', 'Configuración - Sistema de Turnos'); ?>
<?php $__env->startSection('page-title', 'Configuración del Sistema'); ?>

<?php $__env->startSection('content'); ?>
<?php if($settings->isEmpty()): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        No hay configuraciones definidas.
        <form action="<?php echo e(route('admin.settings.initialize')); ?>" method="POST" class="d-inline">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-warning btn-sm ml-2">
                <i class="fas fa-cog mr-1"></i>Inicializar Configuración
            </button>
        </form>
    </div>
<?php else: ?>
    <form action="<?php echo e(route('admin.settings.update')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $groupSettings): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cog mr-2"></i>
                    <?php echo e(ucfirst($group)); ?>

                </div>
                <div class="card-body">
                    <div class="row">
                        <?php $__currentLoopData = $groupSettings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-6 mb-3">
                                <label for="settings_<?php echo e($setting->key); ?>">
                                    <?php echo e($setting->description ?? $setting->key); ?>

                                </label>

                                <?php if($setting->type === 'boolean'): ?>
                                    <div class="custom-control custom-switch">
                                        <input type="hidden" name="settings[<?php echo e($setting->key); ?>]" value="0">
                                        <input type="checkbox" class="custom-control-input"
                                               id="settings_<?php echo e($setting->key); ?>"
                                               name="settings[<?php echo e($setting->key); ?>]"
                                               value="1"
                                               <?php echo e($setting->value ? 'checked' : ''); ?>>
                                        <label class="custom-control-label" for="settings_<?php echo e($setting->key); ?>">
                                            <?php echo e($setting->value ? 'Habilitado' : 'Deshabilitado'); ?>

                                        </label>
                                    </div>
                                <?php elseif($setting->type === 'integer'): ?>
                                    <input type="number" class="form-control"
                                           id="settings_<?php echo e($setting->key); ?>"
                                           name="settings[<?php echo e($setting->key); ?>]"
                                           value="<?php echo e($setting->value); ?>">
                                <?php elseif($setting->key === 'opening_time' || $setting->key === 'closing_time'): ?>
                                    <input type="time" class="form-control"
                                           id="settings_<?php echo e($setting->key); ?>"
                                           name="settings[<?php echo e($setting->key); ?>]"
                                           value="<?php echo e($setting->value); ?>">
                                <?php elseif($setting->type === 'json'): ?>
                                    <textarea class="form-control"
                                              id="settings_<?php echo e($setting->key); ?>"
                                              name="settings[<?php echo e($setting->key); ?>]"
                                              rows="2"><?php echo e(is_array($setting->value) ? json_encode($setting->value) : $setting->value); ?></textarea>
                                    <small class="form-text text-muted">Formato JSON</small>
                                <?php else: ?>
                                    <input type="text" class="form-control"
                                           id="settings_<?php echo e($setting->key); ?>"
                                           name="settings[<?php echo e($setting->key); ?>]"
                                           value="<?php echo e($setting->value); ?>">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <div class="card">
            <div class="card-body">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i>Guardar Configuración
                </button>
                <form action="<?php echo e(route('admin.settings.initialize')); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-outline-secondary ml-2">
                        <i class="fas fa-redo mr-1"></i>Restaurar Valores por Defecto
                    </button>
                </form>
            </div>
        </div>
    </form>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\turnero_app\turneroapp\resources\views/admin/settings/index.blade.php ENDPATH**/ ?>