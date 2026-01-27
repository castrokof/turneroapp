

<?php $__env->startSection('title', 'Usuarios - Sistema de Turnos'); ?>
<?php $__env->startSection('page-title', 'Gestión de Usuarios'); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-users mr-2"></i>Lista de Usuarios</span>
        <a href="<?php echo e(route('admin.users.create')); ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i>Nuevo Usuario
        </a>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form action="<?php echo e(route('admin.users.index')); ?>" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o email..."
                           value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-2 mb-2">
                    <select name="role" class="form-control">
                        <option value="">Todos los roles</option>
                        <option value="admin" <?php echo e(request('role') === 'admin' ? 'selected' : ''); ?>>Administrador</option>
                        <option value="agent" <?php echo e(request('role') === 'agent' ? 'selected' : ''); ?>>Agente</option>
                        <option value="viewer" <?php echo e(request('role') === 'viewer' ? 'selected' : ''); ?>>Visualizador</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <select name="status" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Activo</option>
                        <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>Inactivo</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <button type="submit" class="btn btn-secondary btn-block">
                        <i class="fas fa-search mr-1"></i>Filtrar
                    </button>
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Último Acceso</th>
                        <th width="150">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <i class="fas fa-user-circle mr-2 text-<?php echo e($user->is_active ? 'success' : 'secondary'); ?>"></i>
                                <?php echo e($user->name); ?>

                            </td>
                            <td><?php echo e($user->email); ?></td>
                            <td>
                                <?php switch($user->role):
                                    case ('admin'): ?>
                                        <span class="badge badge-danger">Administrador</span>
                                        <?php break; ?>
                                    <?php case ('agent'): ?>
                                        <span class="badge badge-primary">Agente</span>
                                        <?php break; ?>
                                    <?php default: ?>
                                        <span class="badge badge-secondary">Visualizador</span>
                                <?php endswitch; ?>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo e($user->is_active ? 'success' : 'secondary'); ?>">
                                    <?php echo e($user->is_active ? 'Activo' : 'Inactivo'); ?>

                                </span>
                            </td>
                            <td>
                                <?php echo e($user->last_login_at ? $user->last_login_at->diffForHumans() : 'Nunca'); ?>

                            </td>
                            <td>
                                <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if($user->id !== auth()->id()): ?>
                                    <button type="button" class="btn btn-sm btn-outline-<?php echo e($user->is_active ? 'warning' : 'success'); ?>"
                                            onclick="toggleStatus(<?php echo e($user->id); ?>)" title="<?php echo e($user->is_active ? 'Desactivar' : 'Activar'); ?>">
                                        <i class="fas fa-<?php echo e($user->is_active ? 'ban' : 'check'); ?>"></i>
                                    </button>
                                    <form action="<?php echo e(route('admin.users.destroy', $user)); ?>" method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Está seguro de eliminar este usuario?')">
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
                            <td colspan="6" class="text-center text-muted">No se encontraron usuarios</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php echo e($users->withQueryString()->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function toggleStatus(userId) {
    if (!confirm('¿Está seguro de cambiar el estado del usuario?')) return;

    $.post(`/admin/users/${userId}/toggle-status`)
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

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\turneroapp\resources\views/admin/users/index.blade.php ENDPATH**/ ?>