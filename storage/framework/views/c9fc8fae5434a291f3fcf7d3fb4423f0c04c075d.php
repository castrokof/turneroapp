<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla de Turnos - <?php echo e($settings['business_name']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #fff;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }
        .header {
            background: rgba(0,0,0,0.3);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 1.8rem;
        }
        .clock {
            font-size: 2rem;
            font-family: monospace;
        }
        .main-content {
            padding: 2rem;
            height: calc(100vh - 80px);
            display: flex;
            gap: 2rem;
        }
        .current-section {
            flex: 1;
        }
        .pending-section {
            width: 400px;
        }
        .current-ticket-card {
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 1.5rem;
            animation: pulse 2s infinite;
        }
        @keyframes  pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
        .current-ticket-card .ticket-number {
            font-size: 6rem;
            font-weight: bold;
            line-height: 1;
            text-shadow: 0 0 20px rgba(255,255,255,0.3);
        }
        .current-ticket-card .window-name {
            font-size: 2.5rem;
            margin-top: 1rem;
            color: #ffc107;
        }
        .current-ticket-card .service-name {
            font-size: 1.5rem;
            opacity: 0.8;
        }
        .pending-list {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 1rem;
            height: 100%;
            overflow: hidden;
        }
        .pending-list h3 {
            text-align: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .pending-item {
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 0.8rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .pending-item .ticket {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .stats-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.5);
            padding: 0.8rem 2rem;
            display: flex;
            justify-content: space-around;
        }
        .stat-item {
            text-align: center;
        }
        .stat-item .value {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .stat-item .label {
            font-size: 0.9rem;
            opacity: 0.7;
        }
        .window-status {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .window-card {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
        }
        .window-card.active {
            background: rgba(40, 167, 69, 0.3);
            border: 2px solid #28a745;
        }
        .window-card .window-name {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }
        .window-card .current-ticket {
            font-size: 2.5rem;
            font-weight: bold;
        }
        .no-queues {
            text-align: center;
            padding: 3rem;
            opacity: 0.6;
        }
        .no-queues i {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        /* Activation Overlay */
        .activation-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            cursor: pointer;
        }
        .activation-overlay.hidden { display: none; }
        .activation-content { text-align: center; }
        .activation-content i { font-size: 6rem; margin-bottom: 1.5rem; animation: pulse 2s infinite; }
        .activation-content h1 { font-size: 2.5rem; margin-bottom: 0.5rem; }
        .activation-content p { font-size: 1.2rem; opacity: 0.7; }
        .activation-content .click-hint {
            margin-top: 1.5rem;
            padding: 0.8rem 2rem;
            background: rgba(255,255,255,0.1);
            border-radius: 30px;
            animation: bounce 1s infinite;
        }
        @keyframes  bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
    </style>
</head>
<body>
    <!-- Activation Overlay -->
    <div class="activation-overlay" id="activationOverlay">
        <div class="activation-content">
            <i class="fas fa-volume-up"></i>
            <h1>Pantalla de Turnos</h1>
            <p><?php echo e($settings['business_name']); ?></p>
            <div class="click-hint">
                <i class="fas fa-hand-pointer mr-2"></i>
                Clic para activar sonido
            </div>
        </div>
    </div>

    <div class="header">
        <h1><i class="fas fa-ticket-alt mr-3"></i><?php echo e($settings['business_name']); ?></h1>
        <div class="clock" id="clock">--:--:--</div>
    </div>

    <div class="main-content">
        <div class="current-section">
            <h2 class="mb-4"><i class="fas fa-bullhorn mr-2"></i>Turnos en Atención</h2>
            <div class="window-status" id="windowStatus">
                <!-- Dynamic content -->
            </div>
        </div>

        <div class="pending-section">
            <div class="pending-list">
                <h3><i class="fas fa-clock mr-2"></i>Próximos Turnos</h3>
                <div id="pendingList">
                    <!-- Dynamic content -->
                </div>
            </div>
        </div>
    </div>

    <div class="stats-bar">
        <div class="stat-item">
            <div class="value" id="statTotal">0</div>
            <div class="label">Total Hoy</div>
        </div>
        <div class="stat-item">
            <div class="value" id="statPending">0</div>
            <div class="label">En Espera</div>
        </div>
        <div class="stat-item">
            <div class="value" id="statCompleted">0</div>
            <div class="label">Atendidos</div>
        </div>
        <div class="stat-item">
            <div class="value"><?php echo e(now()->format('d/m/Y')); ?></div>
            <div class="label">Fecha</div>
        </div>
    </div>

    <!-- Audio for notification -->
    <audio id="notificationSound" preload="auto">
        <source src="<?php echo e(asset('sounds/notification.mp3')); ?>" type="audio/mpeg">
    </audio>

    <script src="<?php echo e(asset('js/jquery-3.6.0.min.js')); ?>"></script>
    <script>
        const REFRESH_RATE = <?php echo e($settings['refresh_rate']); ?>;
        const SHOW_NEXT = <?php echo e($settings['show_next']); ?>;
        const SOUND_ENABLED = <?php echo e($settings['sound_enabled'] ? 'true' : 'false'); ?>;
        const VOICE_ENABLED = <?php echo e($settings['voice_enabled'] ? 'true' : 'false'); ?>;

        let lastCalledId = null;
        let lastCalledTimestamp = 0;
        let audioEnabled = false;

        // Activation overlay
        document.getElementById('activationOverlay').addEventListener('click', function() {
            const audio = document.getElementById('notificationSound');
            audio.play().then(() => {
                audio.pause();
                audio.currentTime = 0;
                audioEnabled = true;
            }).catch(() => {
                audioEnabled = false;
            });
            this.classList.add('hidden');
        });

        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent = now.toLocaleTimeString('es-ES');
        }

        function playNotification(ticketNumber, windowName) {
            if (SOUND_ENABLED && audioEnabled) {
                const audio = document.getElementById('notificationSound');
                audio.currentTime = 0;
                audio.play().catch(e => console.log('Audio blocked'));
            }

            if (VOICE_ENABLED && 'speechSynthesis' in window) {
                speechSynthesis.cancel();
                const utterance = new SpeechSynthesisUtterance(`Turno ${ticketNumber}, ${windowName}`);
                utterance.lang = 'es-ES';
                utterance.rate = 0.9;
                setTimeout(() => speechSynthesis.speak(utterance), 500);
            }
        }

        function fetchData() {
            $.get('<?php echo e(route("display.data")); ?>', { show_next: SHOW_NEXT })
                .done(function(data) {
                    // Update windows
                    let windowsHtml = '';
                    if (data.windows.length === 0) {
                        windowsHtml = '<div class="no-queues"><i class="fas fa-door-closed"></i><p>No hay ventanillas activas</p></div>';
                    } else {
                        data.windows.forEach(function(window) {
                            const isActive = window.current_ticket !== null;
                            windowsHtml += `
                                <div class="window-card ${isActive ? 'active' : ''}">
                                    <div class="window-name">${window.name}</div>
                                    <div class="current-ticket">${window.current_ticket || '-'}</div>
                                    <small>${window.agent}</small>
                                </div>
                            `;
                        });
                    }
                    $('#windowStatus').html(windowsHtml);

                    // Update pending list
                    let pendingHtml = '';
                    if (data.pending.length === 0) {
                        pendingHtml = '<div class="text-center text-muted py-4"><i class="fas fa-check-circle fa-2x mb-2"></i><br>No hay turnos pendientes</div>';
                    } else {
                        data.pending.forEach(function(queue) {
                            pendingHtml += `
                                <div class="pending-item">
                                    <span class="ticket" style="color: ${queue.service_color}">${queue.ticket_number}</span>
                                    <span class="badge badge-light">${queue.service_name}</span>
                                </div>
                            `;
                        });
                    }
                    $('#pendingList').html(pendingHtml);

                    // Update stats
                    $('#statTotal').text(data.stats.total);
                    $('#statPending').text(data.stats.pending);
                    $('#statCompleted').text(data.stats.completed);

                    // Check for new call
                    if (data.last_called && data.last_called.called_at > lastCalledTimestamp) {
                        lastCalledTimestamp = data.last_called.called_at;
                        playNotification(data.last_called.ticket_number, data.last_called.window);
                    }
                });
        }

        // Initialize
        updateClock();
        setInterval(updateClock, 1000);
        fetchData();
        setInterval(fetchData, REFRESH_RATE);
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\turneroapp\resources\views/display/index.blade.php ENDPATH**/ ?>