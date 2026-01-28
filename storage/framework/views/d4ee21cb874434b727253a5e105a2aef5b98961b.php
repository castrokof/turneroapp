<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Generar Turno - <?php echo e($settings['business_name']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }
        body {
            background: linear-gradient(135deg, #667eea 0%, #C3ABDB 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }

        /* ========== WELCOME SCREEN (Step 0) ========== */
        .welcome-screen {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #1e3c72 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff;
            cursor: pointer;
            z-index: 100;
            transition: opacity 0.5s, visibility 0.5s;
        }

        .welcome-screen.hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .welcome-logo {
            margin-bottom: 2rem;
        }

        .welcome-logo img {
            max-width: 250px;
            max-height: 180px;
            object-fit: contain;
        }

        .welcome-logo .logo-placeholder {
            font-size: 10rem;
            opacity: 0.9;
        }

        .welcome-title {
            font-size: 3.5rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .welcome-subtitle {
            font-size: 1.8rem;
            opacity: 0.9;
            margin-bottom: 4rem;
            text-align: center;
        }

        .welcome-touch {
            padding: 2rem 4rem;
            background: rgba(255,255,255,0.2);
            border: 3px solid rgba(255,255,255,0.5);
            border-radius: 20px;
            animation: pulse-border 2s infinite;
        }

        .welcome-touch-text {
            font-size: 2.5rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .welcome-touch-text i {
            animation: tap-animation 1.5s infinite;
        }

        @keyframes  pulse-border {
            0%, 100% {
                border-color: rgba(255,255,255,0.5);
                box-shadow: 0 0 20px rgba(255,255,255,0.2);
            }
            50% {
                border-color: rgba(255,255,255,0.9);
                box-shadow: 0 0 40px rgba(255,255,255,0.4);
            }
        }

        @keyframes  tap-animation {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        /* ========== MAIN KIOSK ========== */
        .kiosk-container {
            width: 100%;
            max-width: 1200px;
            padding: 2rem;
        }

        .kiosk-header {
            text-align: center;
            color: #fff;
            margin-bottom: 2rem;
        }

        .kiosk-header h1 {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }

        .kiosk-header p {
            font-size: 1.5rem;
            opacity: 0.9;
        }

        .kiosk-card {
            background: #fff;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .kiosk-body {
            padding: 3rem;
        }

        /* ========== SERVICE BUTTONS (Step 1) ========== */
        .service-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .service-btn {
            border: none;
            border-radius: 25px;
            padding: 3rem 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 220px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        .service-btn:hover, .service-btn:active {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }

        .service-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .service-btn .prefix {
            font-size: 4rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .service-btn .name {
            font-size: 1.6rem;
            font-weight: 500;
        }

        .service-btn .time {
            font-size: 1.1rem;
            opacity: 0.85;
            margin-top: 0.8rem;
        }

        .service-btn .limit-reached {
            margin-top: 1rem;
            padding: 0.5rem 1rem;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            font-size: 1rem;
        }

        /* ========== STEP INDICATOR ========== */
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2.5rem;
        }

        .step {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 0.5rem;
            font-weight: bold;
            font-size: 1.3rem;
            transition: all 0.3s;
        }

        .step.active {
            background: #667eea;
            color: #fff;
            transform: scale(1.1);
        }

        .step.completed {
            background: #28a745;
            color: #fff;
        }

        .step-line {
            width: 60px;
            height: 4px;
            background: #e9ecef;
            align-self: center;
        }

        .step-line.completed {
            background: #28a745;
        }

        #step1, #step2, #step3 {
            display: none;
        }

        #step1.active, #step2.active, #step3.active {
            display: block;
        }

        /* ========== PRIORITY OPTIONS (Step 2) ========== */
        .priority-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .priority-btn {
            padding: 2rem;
            border: 3px solid #dee2e6;
            border-radius: 20px;
            background: #fff;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1.3rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 150px;
        }

        .priority-btn:hover, .priority-btn:active {
            border-color: #667eea;
            background: #f0f3ff;
            transform: scale(1.02);
        }

        .priority-btn i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .priority-btn .priority-name {
            font-weight: 600;
        }

        /* ========== TICKET RESULT (Step 3) ========== */
        .ticket-result {
            text-align: center;
            padding: 3rem;
        }

        .ticket-result .success-icon {
            font-size: 6rem;
            color: #28a745;
            margin-bottom: 1.5rem;
        }

        .ticket-result h3 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .ticket-result .ticket-number {
            font-size: 8rem;
            font-weight: bold;
            margin: 1.5rem 0;
            animation: pulse 1s infinite;
            text-shadow: 3px 3px 6px rgba(0,0,0,0.1);
        }

        .ticket-result .service-name {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .ticket-result .info {
            font-size: 1.4rem;
            color: #6c757d;
        }

        .ticket-result .info p {
            margin-bottom: 0.8rem;
        }

        .btn-back {
            position: absolute;
            top: 1.5rem;
            left: 1.5rem;
            font-size: 1.3rem;
            padding: 0.8rem 1.5rem;
        }

        @keyframes  pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .countdown {
            margin-top: 2rem;
            font-size: 1.4rem;
            color: #6c757d;
        }

        .countdown span {
            font-weight: bold;
            color: #667eea;
            font-size: 1.8rem;
        }

        /* ========== SECTION TITLES ========== */
        .section-title {
            font-size: 2.2rem;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .section-subtitle {
            font-size: 1.3rem;
            color: #6c757d;
            text-align: center;
            margin-bottom: 2rem;
        }

        /* ========== LOADING ========== */
        .loading-spinner {
            text-align: center;
            padding: 4rem;
        }

        .loading-spinner i {
            font-size: 5rem;
            color: #667eea;
            margin-bottom: 1.5rem;
        }

        .loading-spinner h4 {
            font-size: 1.8rem;
        }

        /* ========== HOME BUTTON ========== */
        .btn-home {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: rgba(255,255,255,0.9);
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            font-size: 1.8rem;
            color: #667eea;
            cursor: pointer;
            transition: all 0.3s;
            z-index: 50;
        }

        .btn-home:hover {
            transform: scale(1.1);
            background: #fff;
        }
    </style>
</head>
<body>
    <!-- WELCOME SCREEN (Step 0) -->
    <div class="welcome-screen" id="welcomeScreen" onclick="startKiosk()">
        <div class="welcome-logo">
            <?php if(isset($settings['tv_logo_url']) && $settings['tv_logo_url']): ?>
                <img src="<?php echo e($settings['tv_logo_url']); ?>" alt="Logo">
            <?php else: ?>
                <i class="fas fa-hospital logo-placeholder"></i>
            <?php endif; ?>
        </div>

        <h1 class="welcome-title"><?php echo e($settings['business_name']); ?></h1>

        <?php if(isset($settings['kiosk_message']) && $settings['kiosk_message']): ?>
            <p class="welcome-subtitle"><?php echo e($settings['kiosk_message']); ?></p>
        <?php else: ?>
            <p class="welcome-subtitle">Sistema de Turnos</p>
        <?php endif; ?>

        <div class="welcome-touch">
            <div class="welcome-touch-text">
                <i class="fas fa-hand-pointer"></i>
                Toque aquí para obtener su turno
            </div>
        </div>
    </div>

    <!-- MAIN KIOSK -->
    <div class="kiosk-container">
        <div class="kiosk-header">
            <h1><i class="fas fa-ticket-alt mr-3"></i><?php echo e($settings['business_name']); ?></h1>
            <p>Seleccione el servicio que necesita</p>
        </div>

        <div class="kiosk-card">
            <div class="kiosk-body">
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step active" id="stepIcon1">1</div>
                    <div class="step-line" id="stepLine1"></div>
                    <div class="step" id="stepIcon2">2</div>
                    <div class="step-line" id="stepLine2"></div>
                    <div class="step" id="stepIcon3">3</div>
                </div>

                <!-- Step 1: Select Service -->
                <div id="step1" class="active">
                    <h3 class="section-title">Seleccione el Servicio</h3>
                    <p class="section-subtitle">Toque el botón del servicio que necesita</p>
                    <div class="service-grid">
                        <?php $__currentLoopData = $serviceTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" class="service-btn" style="background-color: <?php echo e($service->color); ?>"
                                    onclick="selectService(<?php echo e($service->id); ?>, '<?php echo e($service->name); ?>', '<?php echo e($service->color); ?>')"
                                    <?php echo e($service->isDailyLimitReached() ? 'disabled' : ''); ?>>
                                <span class="prefix"><?php echo e($service->prefix); ?></span>
                                <span class="name"><?php echo e($service->name); ?></span>
                                <span class="time"><i class="fas fa-clock mr-1"></i>~<?php echo e($service->estimated_time); ?> min</span>
                                <?php if($service->isDailyLimitReached()): ?>
                                    <span class="limit-reached"><i class="fas fa-ban mr-1"></i>Cupos agotados</span>
                                <?php endif; ?>
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <!-- Step 2: Priority Selection -->
                <div id="step2">
                    <button class="btn btn-light btn-back" onclick="goToStep(1)">
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </button>
                    <h3 class="section-title">¿Tiene alguna condición especial?</h3>
                    <p class="section-subtitle">Seleccione si aplica para atención prioritaria</p>

                    <div class="priority-options">
                        <button type="button" class="priority-btn" onclick="selectPriority('normal')">
                            <i class="fas fa-user text-secondary"></i>
                            <span class="priority-name">Atención Normal</span>
                        </button>
                        <button type="button" class="priority-btn" onclick="selectPriority('priority', 'elderly')">
                            <i class="fas fa-user-clock text-info"></i>
                            <span class="priority-name">Adulto Mayor (60+)</span>
                        </button>
                        <button type="button" class="priority-btn" onclick="selectPriority('priority', 'pregnant')">
                            <i class="fas fa-baby" style="color: #e83e8c;"></i>
                            <span class="priority-name">Embarazada</span>
                        </button>
                        <button type="button" class="priority-btn" onclick="selectPriority('priority', 'disability')">
                            <i class="fas fa-wheelchair text-warning"></i>
                            <span class="priority-name">Discapacidad</span>
                        </button>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-muted" style="font-size: 1.1rem;">
                            <i class="fas fa-info-circle mr-1"></i>
                            Los turnos prioritarios tienen preferencia en la cola
                        </p>
                    </div>
                </div>

                <!-- Step 3: Ticket Generated -->
                <div id="step3">
                    <div class="ticket-result">
                        <i class="fas fa-check-circle success-icon"></i>
                        <h3>¡Su turno ha sido generado!</h3>
                        <div class="ticket-number" id="ticketNumber" style="color: #667eea;">---</div>
                        <div class="service-name" id="serviceName">---</div>
                        <div class="info">
                            <p><i class="fas fa-users mr-2"></i>Personas delante de usted: <strong id="pendingCount">0</strong></p>
                            <p><i class="fas fa-clock mr-2"></i>Tiempo estimado: <strong id="estimatedTime">0</strong> minutos</p>
                        </div>
                        <hr>
                        <div class="countdown">
                            <i class="fas fa-redo-alt mr-2"></i>
                            Regresando al inicio en <span id="countdownTimer">10</span> segundos
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Home button -->
    <button class="btn-home" onclick="goHome()" title="Volver al inicio">
        <i class="fas fa-home"></i>
    </button>

    <script src="<?php echo e(asset('js/jquery-3.6.0.min.js')); ?>"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let selectedService = null;
        let selectedServiceName = '';
        let selectedServiceColor = '';
        let generatedQueueId = null;
        let countdownInterval = null;
        let inactivityTimeout = null;
        const INACTIVITY_TIME = 60000; // 60 seconds of inactivity

        // Store original step 2 content
        const step2OriginalContent = $('#step2').html();

        function startKiosk() {
            $('#welcomeScreen').addClass('hidden');
            resetInactivityTimer();
        }

        function goHome() {
            // Clear any intervals
            if (countdownInterval) clearInterval(countdownInterval);
            if (inactivityTimeout) clearTimeout(inactivityTimeout);

            // Reset state
            selectedService = null;
            selectedServiceName = '';
            selectedServiceColor = '';
            generatedQueueId = null;

            // Reset UI
            $('#ticketNumber').text('---');
            $('#serviceName').text('---');
            $('#pendingCount').text('0');
            $('#estimatedTime').text('0');
            $('#step2').html(step2OriginalContent);

            goToStep(1);

            // Show welcome screen
            $('#welcomeScreen').removeClass('hidden');
        }

        function resetInactivityTimer() {
            if (inactivityTimeout) clearTimeout(inactivityTimeout);
            inactivityTimeout = setTimeout(goHome, INACTIVITY_TIME);
        }

        // Reset timer on any touch/click
        $(document).on('click touchstart', function() {
            if ($('#welcomeScreen').hasClass('hidden')) {
                resetInactivityTimer();
            }
        });

        function selectService(serviceId, serviceName, serviceColor) {
            selectedService = serviceId;
            selectedServiceName = serviceName;
            selectedServiceColor = serviceColor;
            goToStep(2);
        }

        function selectPriority(priority, condition = null) {
            // Show loading
            $('#step2').html(`
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                    <h4>Generando su turno...</h4>
                    <p class="text-muted" style="font-size: 1.2rem;">Por favor espere</p>
                </div>
            `);

            // Generate ticket
            $.post('<?php echo e(route("queue.store")); ?>', {
                service_type_id: selectedService,
                priority: priority
            })
            .done(function(response) {
                if (response.success) {
                    generatedQueueId = response.data.queue.id;
                    $('#ticketNumber').text(response.data.ticket_number).css('color', response.data.service_color);
                    $('#serviceName').text(response.data.service_name);
                    $('#pendingCount').text(response.data.pending_count);
                    $('#estimatedTime').text(response.data.estimated_wait);

                    goToStep(3);
                    startCountdown();
                }
            })
            .fail(function(xhr) {
                let message = xhr.responseJSON?.message || 'Error al generar el turno';

                // Restore step 2 content
                $('#step2').html(step2OriginalContent);

                alert(message);

                setTimeout(() => {
                    goToStep(1);
                }, 2000);
            });
        }

        function goToStep(step) {
            $('#step1, #step2, #step3').removeClass('active');
            $(`#step${step}`).addClass('active');

            // Update step indicators
            for (let i = 1; i <= 3; i++) {
                $(`#stepIcon${i}`).removeClass('active completed');
                if (i < step) {
                    $(`#stepIcon${i}`).addClass('completed').html('<i class="fas fa-check"></i>');
                } else if (i === step) {
                    $(`#stepIcon${i}`).addClass('active').text(i);
                } else {
                    $(`#stepIcon${i}`).text(i);
                }
            }

            // Update step lines
            $('#stepLine1').toggleClass('completed', step > 1);
            $('#stepLine2').toggleClass('completed', step > 2);

            // Restore step 2 content if going back
            if (step === 2) {
                $('#step2').html(step2OriginalContent);
            }
        }

        function startCountdown() {
            let seconds = 10;
            $('#countdownTimer').text(seconds);

            countdownInterval = setInterval(function() {
                seconds--;
                $('#countdownTimer').text(seconds);

                if (seconds <= 0) {
                    clearInterval(countdownInterval);
                    goHome();
                }
            }, 1000);
        }
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\turneroapp\resources\views/display/kiosk.blade.php ENDPATH**/ ?>