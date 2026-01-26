<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla TV - {{ $settings['business_name'] }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #000;
            color: #fff;
            height: 100vh;
            overflow: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container-fluid {
            height: 100vh;
            padding: 0;
        }
        .row {
            height: 100%;
            margin: 0;
        }
        .left-panel {
            background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%);
            padding: 2rem;
            display: flex;
            flex-direction: column;
        }
        .right-panel {
            background: #111;
            padding: 1rem;
            display: flex;
            flex-direction: column;
        }
        .header-info {
            text-align: center;
            padding-bottom: 1rem;
            border-bottom: 2px solid rgba(255,255,255,0.2);
            margin-bottom: 2rem;
        }
        .header-info h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .header-info .clock {
            font-size: 3rem;
            font-family: monospace;
        }
        .current-turn-display {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .current-turn-display .label {
            font-size: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            opacity: 0.8;
            margin-bottom: 1rem;
        }
        .current-turn-display .ticket-number {
            font-size: 12rem;
            font-weight: 900;
            line-height: 1;
            text-shadow: 0 0 50px rgba(255,255,255,0.5);
            animation: glow 2s ease-in-out infinite alternate;
        }
        @keyframes glow {
            from { text-shadow: 0 0 20px rgba(255,255,255,0.3); }
            to { text-shadow: 0 0 60px rgba(255,255,255,0.8); }
        }
        .current-turn-display .window-info {
            font-size: 3rem;
            margin-top: 2rem;
            color: #ffc107;
        }
        .next-turns-header {
            background: #222;
            padding: 1rem;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        .next-turns-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }
        .next-turns-list {
            flex: 1;
            overflow: hidden;
        }
        .next-turn-item {
            background: #222;
            border-radius: 10px;
            padding: 1rem 1.5rem;
            margin-bottom: 0.8rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .next-turn-item .ticket {
            font-size: 2rem;
            font-weight: bold;
        }
        .next-turn-item .service {
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        .windows-strip {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            background: rgba(0,0,0,0.3);
            border-radius: 10px;
            margin-top: auto;
        }
        .window-mini {
            flex: 1;
            background: #333;
            border-radius: 8px;
            padding: 0.8rem;
            text-align: center;
        }
        .window-mini.active {
            background: #28a745;
        }
        .window-mini .name {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        .window-mini .ticket {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .no-queue {
            opacity: 0.5;
        }
        .no-queue i {
            font-size: 6rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 left-panel">
                <div class="header-info">
                    <h1><i class="fas fa-hospital mr-2"></i>{{ $settings['business_name'] }}</h1>
                    <div class="clock" id="clock">--:--:--</div>
                </div>

                <div class="current-turn-display" id="currentTurnDisplay">
                    <div class="no-queue">
                        <i class="fas fa-hourglass-half"></i>
                        <h2>Esperando turnos...</h2>
                    </div>
                </div>

                <div class="windows-strip" id="windowsStrip">
                    <!-- Dynamic windows -->
                </div>
            </div>

            <div class="col-md-4 right-panel">
                <div class="next-turns-header">
                    <h2><i class="fas fa-list-ol mr-2"></i>Próximos Turnos</h2>
                </div>

                <div class="next-turns-list" id="nextTurnsList">
                    <!-- Dynamic list -->
                </div>
            </div>
        </div>
    </div>

    <audio id="notificationSound" preload="auto">
        <source src="https://assets.mixkit.co/sfx/preview/mixkit-bell-notification-933.mp3" type="audio/mpeg">
    </audio>

    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script>
        const REFRESH_RATE = {{ $settings['refresh_rate'] }};
        const SHOW_NEXT = {{ $settings['show_next'] }};
        const SOUND_ENABLED = {{ $settings['sound_enabled'] ? 'true' : 'false' }};
        const VOICE_ENABLED = {{ $settings['voice_enabled'] ? 'true' : 'false' }};

        let lastCalledTimestamp = 0;

        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent = now.toLocaleTimeString('es-ES');
        }

        function playNotification(ticketNumber, windowName) {
            if (SOUND_ENABLED) {
                document.getElementById('notificationSound').play();
            }

            if (VOICE_ENABLED && 'speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance(`Turno ${ticketNumber}, pasar a ${windowName}`);
                utterance.lang = 'es-ES';
                utterance.rate = 0.8;
                utterance.volume = 1;
                speechSynthesis.speak(utterance);
            }
        }

        function fetchData() {
            $.get('{{ route("display.data") }}', { show_next: SHOW_NEXT })
                .done(function(data) {
                    // Current turn display
                    let currentHtml = '';
                    const activeTurns = data.current.filter(q => q.status === 'called');

                    if (activeTurns.length > 0) {
                        const current = activeTurns[0];
                        currentHtml = `
                            <div class="label">Turno Actual</div>
                            <div class="ticket-number" style="color: ${current.service_color}">${current.ticket_number}</div>
                            <div class="window-info"><i class="fas fa-arrow-right mr-2"></i>${current.window_name}</div>
                        `;
                    } else {
                        currentHtml = `
                            <div class="no-queue">
                                <i class="fas fa-hourglass-half"></i>
                                <h2>Esperando turnos...</h2>
                            </div>
                        `;
                    }
                    $('#currentTurnDisplay').html(currentHtml);

                    // Windows strip
                    let windowsHtml = '';
                    data.windows.forEach(function(window) {
                        const isActive = window.current_ticket !== null;
                        windowsHtml += `
                            <div class="window-mini ${isActive ? 'active' : ''}">
                                <div class="name">${window.name}</div>
                                <div class="ticket">${window.current_ticket || '-'}</div>
                            </div>
                        `;
                    });
                    $('#windowsStrip').html(windowsHtml);

                    // Next turns list
                    let nextHtml = '';
                    if (data.pending.length === 0) {
                        nextHtml = '<div class="text-center text-muted py-5"><i class="fas fa-check-circle fa-3x mb-3"></i><br>No hay turnos pendientes</div>';
                    } else {
                        data.pending.forEach(function(queue) {
                            nextHtml += `
                                <div class="next-turn-item">
                                    <span class="ticket" style="color: ${queue.service_color}">${queue.ticket_number}</span>
                                    <span class="service" style="background-color: ${queue.service_color}">${queue.service_name}</span>
                                </div>
                            `;
                        });
                    }
                    $('#nextTurnsList').html(nextHtml);

                    // Check for new call
                    if (data.last_called && data.last_called.called_at > lastCalledTimestamp) {
                        lastCalledTimestamp = data.last_called.called_at;
                        playNotification(data.last_called.ticket_number, data.last_called.window);
                    }
                });
        }

        updateClock();
        setInterval(updateClock, 1000);
        fetchData();
        setInterval(fetchData, REFRESH_RATE);
    </script>
</body>
</html>
