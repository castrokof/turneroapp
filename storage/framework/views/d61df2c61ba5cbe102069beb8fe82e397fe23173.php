<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Sistema de Turnos'); ?></title>

    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #007bff;
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #343a40 0%, #212529 100%);
            padding-top: 0;
            z-index: 1000;
            transition: all 0.3s;
        }

        .sidebar .brand {
            padding: 1rem;
            background: rgba(0,0,0,0.2);
            color: #fff;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar .brand h4 {
            margin: 0;
            font-size: 1.1rem;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.8rem 1.2rem;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-left-color: var(--primary-color);
        }

        .sidebar .nav-link i {
            width: 25px;
            margin-right: 10px;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .top-navbar {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 0.8rem 1.5rem;
        }

        .content-wrapper {
            padding: 1.5rem;
        }

        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
            border-radius: 8px;
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #eee;
            font-weight: 600;
        }

        .stat-card {
            border-left: 4px solid;
        }

        .stat-card.primary { border-left-color: #007bff; }
        .stat-card.success { border-left-color: #28a745; }
        .stat-card.warning { border-left-color: #ffc107; }
        .stat-card.danger { border-left-color: #dc3545; }
        .stat-card.info { border-left-color: #17a2b8; }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
        }

        .stat-card .stat-label {
            color: #6c757d;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .btn {
            border-radius: 5px;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
        }

        .badge-priority {
            padding: 0.4em 0.8em;
        }

        .user-dropdown img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            .sidebar.active {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="brand">
            <h4><i class="fas fa-ticket-alt mr-2"></i>Turnero</h4>
        </div>

        <ul class="nav flex-column mt-3">
            <?php if(auth()->user()->isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('admin.dashboard')); ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.users.index')); ?>">
                        <i class="fas fa-users"></i> Usuarios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.service-types.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.service-types.index')); ?>">
                        <i class="fas fa-concierge-bell"></i> Tipos de Servicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.service-windows.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.service-windows.index')); ?>">
                        <i class="fas fa-window-maximize"></i> Ventanillas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.clients.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.clients.index')); ?>">
                        <i class="fas fa-user-friends"></i> Clientes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.reports.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.reports.index')); ?>">
                        <i class="fas fa-chart-bar"></i> Reportes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.settings.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.settings.index')); ?>">
                        <i class="fas fa-cog"></i> Configuración
                    </a>
                </li>
                <hr class="my-2 mx-3 bg-secondary">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('agent.dashboard')); ?>">
                        <i class="fas fa-headset"></i> Módulo Agente
                    </a>
                </li>
            <?php endif; ?>

            <?php if(auth()->user()->isAgent() && !auth()->user()->isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('agent.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('agent.dashboard')); ?>">
                        <i class="fas fa-headset"></i> Atención
                    </a>
                </li>
            <?php endif; ?>

            <hr class="my-2 mx-3 bg-secondary">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo e(route('display.index')); ?>" target="_blank">
                    <i class="fas fa-tv"></i> Pantalla Pública
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo e(route('display.kiosk')); ?>" target="_blank">
                    <i class="fas fa-tablet-alt"></i> Kiosko
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo e(route('display.tv')); ?>" target="_blank">
                    <i class="fas fa-tv"></i> TV
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div>
                <button class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="ml-2"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></span>
            </div>

            <div class="dropdown">
                <a href="#" class="dropdown-toggle text-dark text-decoration-none" data-toggle="dropdown">
                    <i class="fas fa-user-circle fa-lg mr-2"></i>
                    <?php echo e(auth()->user()->name); ?>

                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <span class="dropdown-item-text text-muted"><?php echo e(ucfirst(auth()->user()->role)); ?></span>
                    <div class="dropdown-divider"></div>
                    <form action="<?php echo e(route('logout')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <div class="content-wrapper">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i><?php echo e(session('success')); ?>

                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i><?php echo e(session('error')); ?>

                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>

    <!-- jQuery -->
    <script src="<?php echo e(asset('js/jquery-3.6.0.min.js')); ?>"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#sidebarToggle').click(function() {
            $('.sidebar').toggleClass('active');
        });
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\turneroapp\resources\views/layouts/app.blade.php ENDPATH**/ ?>