

<?php $__env->startSection('title', 'Clientes - Sistema de Turnos'); ?>
<?php $__env->startSection('page-title', 'Gestión de Clientes'); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-user-friends mr-2"></i>Lista de Clientes</span>
        <a href="<?php echo e(route('admin.clients.create')); ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i>Nuevo Cliente
        </a>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form action="<?php echo e(route('admin.clients.index')); ?>" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <input type="text" name="search" class="form-control"
                           placeholder="Buscar por documento, nombre, email o teléfono..."
                           value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-2 mb-2">
                    <div class="custom-control custom-checkbox mt-2">
                        <input type="checkbox" class="custom-control-input" id="priority" name="priority"
                               <?php echo e(request('priority') ? 'checked' : ''); ?>>
                        <label class="custom-control-label" for="priority">Solo Prioritarios</label>
                    </div>
                </div>
                <div class="col-md-2 mb-2">
                    <button type="submit" class="btn btn-secondary btn-block">
                        <i class="fas fa-search mr-1"></i>Buscar
                    </button>
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Nombre</th>
                        <th>Contacto</th>
                        <th>Prioridad</th>
                        <th width="150">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <strong><?php echo e(strtoupper($client->document_type)); ?></strong>: <?php echo e($client->document_number); ?>

                            </td>
                            <td><?php echo e($client->full_name); ?></td>
                            <td>
                                <?php if($client->email): ?>
                                    <i class="fas fa-envelope text-muted mr-1"></i><?php echo e($client->email); ?><br>
                                <?php endif; ?>
                                <?php if($client->phone): ?>
                                    <i class="fas fa-phone text-muted mr-1"></i><?php echo e($client->phone); ?>

                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($client->is_elderly): ?>
                                    <span class="badge badge-info"><i class="fas fa-user-clock"></i> Adulto Mayor</span>
                                <?php endif; ?>
                                <?php if($client->is_pregnant): ?>
                                    <span class="badge badge-pink" style="background-color: #e83e8c; color: #fff;"><i class="fas fa-baby"></i> Embarazada</span>
                                <?php endif; ?>
                                <?php if($client->has_disability): ?>
                                    <span class="badge badge-warning"><i class="fas fa-wheelchair"></i> Discapacidad</span>
                                <?php endif; ?>
                                <?php if(!$client->hasPriority()): ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('admin.clients.show', $client)); ?>" class="btn btn-sm btn-outline-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('admin.clients.edit', $client)); ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('admin.clients.destroy', $client)); ?>" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Está seguro de eliminar este cliente?')">
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
                            <td colspan="5" class="text-center text-muted">No se encontraron clientes</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php echo e($clients->withQueryString()->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\turneroapp\resources\views/admin/clients/index.blade.php ENDPATH**/ ?>