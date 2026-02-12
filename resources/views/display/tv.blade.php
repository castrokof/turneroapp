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
            background: linear-gradient(135deg, #e8eef5 0%, #d5dde8 100%);
            color: #333;
            height: 100vh;
            overflow: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Main container - 3 column layout */
        .tv-container {
            height: 100vh;
            display: grid;
            grid-template-columns: 1fr 320px;
            grid-template-rows: auto 1fr auto;
            gap: 0;
        }

        /* Header - spans full width */
        .header-bar {
            grid-column: 1 / -1;
            background: linear-gradient(90deg, #1e3c72 0%, #2a5298 100%);
            padding: 0.8rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
        }

        .header-bar .business-name {
            font-size: 1.8rem;
            font-weight: bold;
            color: #fff;
        }

        .header-bar .clock {
            font-size: 2.2rem;
            font-family: monospace;
            color: #fff;
        }

        /* Video/Content Area - left side */
            .video-area {
                flex: 1;
                position: relative;
                background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ed 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
            }

            .video-area video {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                z-index: 1;
            }

            /* Overlay content sobre el video - POSICIÓN SUPERIOR IZQUIERDA */
            .video-area .overlay-content {
                position: absolute;
                top: 2rem;
                left: 2rem;
                z-index: 2;
                display: flex;
                align-items: center;
                gap: 1.5rem;
                background: rgba(0, 0, 0, 0.4);
                padding: 1.2rem 2rem;
                border-radius: 15px;
                backdrop-filter: blur(10px);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            }

            /* Logo - FIJO y estático */
            .video-area .overlay-content .logo-container {
                margin-bottom: 0;
            }

            .video-area .overlay-content .logo-container img {
                height: 80px;
                width: auto;
                object-fit: contain;
                filter: drop-shadow(0 2px 8px rgba(0,0,0,0.5));
                /* Sin animación - queda quieto */
            }

            .video-area .overlay-content .logo-placeholder {
                font-size: 3.5rem;
                color: rgba(255,255,255,0.95);
                text-shadow: 0 0 15px rgba(30, 60, 114, 0.8);
                /* Sin animación - queda quieto */
            }

            /* Mensaje - CON ANIMACIÓN */
            .video-area .overlay-content .tv-message,
            .video-area .overlay-content .default-text {
                font-size: 2.2rem;
                line-height: 1.3;
                font-weight: 600;
                color: #fff;
                margin: 0;
                text-shadow: 1px 1px 6px rgba(0,0,0,0.8);
                white-space: nowrap;
                
                /* Animación: Fade In/Out */
                animation: messageFade 4s ease-in-out infinite;
            }

            /* Animación 1: Fade In/Out (aparecer y desaparecer) */
            @keyframes messageFade {
                0%, 100% {
                    opacity: 0.3;
                    transform: scale(0.95);
                }
                50% {
                    opacity: 1;
                    transform: scale(1);
                }
            }

            /* Animación 2: Slide In/Out (deslizar) */
            /* 
            @keyframes messageSlide {
                0%, 100% {
                    opacity: 0.3;
                    transform: translateX(-30px) scale(0.95);
                }
                50% {
                    opacity: 1;
                    transform: translateX(0) scale(1);
                }
            }
            .video-area .overlay-content .tv-message,
            .video-area .overlay-content .default-text {
                animation: messageSlide 4s ease-in-out infinite;
            }
            */

            /* Animación 3: Typing Effect (máquina de escribir) */
            /* 
            .video-area .overlay-content .tv-message,
            .video-area .overlay-content .default-text {
                overflow: hidden;
                border-right: 3px solid #fff;
                white-space: nowrap;
                animation: typing 3.5s steps(40, end) infinite,
                        blink-caret 0.75s step-end infinite;
            }

            @keyframes typing {
                0% { width: 0; }
                50% { width: 100%; }
                100% { width: 0; }
            }

            @keyframes blink-caret {
                from, to { border-color: transparent; }
                50% { border-color: #fff; }
            }
            */

            /* Animación 4: Pulse (latido) */
            /* 
            @keyframes messagePulse {
                0%, 100% {
                    opacity: 0.7;
                    transform: scale(1);
                }
                50% {
                    opacity: 1;
                    transform: scale(1.05);
                }
            }
            .video-area .overlay-content .tv-message,
            .video-area .overlay-content .default-text {
                animation: messagePulse 3s ease-in-out infinite;
            }
            */

            /* Animación 5: Bounce (rebote) */
            /* 
            @keyframes messageBounce {
                0%, 100% {
                    transform: translateY(0);
                }
                50% {
                    transform: translateY(-15px);
                }
            }
            .video-area .overlay-content .tv-message,
            .video-area .overlay-content .default-text {
                animation: messageBounce 2s ease-in-out infinite;
            }
            */

        /* Windows Panel - right side */
        .windows-panel {
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
            padding: 1rem;
            display: flex;
            flex-direction: column;
            border-left: 3px solid #2a5298;
        }

        .windows-panel-title {
            text-align: center;
            font-size: 1.3rem;
            font-weight: bold;
            padding-bottom: 0.8rem;
            margin-bottom: 0.8rem;
            border-bottom: 2px solid rgba(255,255,255,0.2);
            color: #ffc107;
        }

        .windows-grid {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.6rem;
            overflow-y: auto;
        }

        .window-card {
            background: #333;
            border-radius: 10px;
            padding: 0.8rem;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 80px;
        }

        .window-card.active {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            box-shadow: 0 0 15px rgba(40, 167, 69, 0.5);
        }

        .window-card .window-name {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 0.3rem;
        }

        .window-card .window-ticket {
            font-size: 1.8rem;
            font-weight: bold;
        }

        .window-card.inactive .window-ticket {
            color: #666;
        }

        /* Queue strip at bottom - spans full width */
        .queue-strip {
            grid-column: 1 / -1;
            background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%);
            padding: 1.2rem 2rem;
            display: flex;
            align-items: center;
            gap: 2rem;
            min-height: 100px;
        }

        .queue-strip-title {
            font-size: 1.4rem;
            font-weight: bold;
            white-space: nowrap;
            padding-right: 1.5rem;
            border-right: 3px solid rgba(255,255,255,0.3);
        }

        .queue-strip-title i {
            margin-right: 0.5rem;
        }

        .queue-items {
            display: flex;
            gap: 1.2rem;
            flex: 1;
            overflow-x: auto;
            padding: 0.3rem 0;
        }

        .queue-item {
            background: rgba(255,255,255,0.15);
            border-radius: 12px;
            padding: 1rem 1.8rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            min-width: 180px;
            flex-shrink: 0;
        }

        .queue-item .number {
            font-size: 2.5rem;
            font-weight: bold;
        }

        .queue-item .service-badge {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 1rem;
            font-weight: 500;
        }

        .no-queue-message {
            color: rgba(255,255,255,0.6);
            font-style: italic;
            font-size: 1.3rem;
        }

        /* MODAL for turn call */
        .turn-call-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.9);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .turn-call-modal.show {
            display: flex;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .turn-call-content {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #1e3c72 100%);
            border-radius: 30px;
            padding: 3rem 5rem;
            text-align: center;
            box-shadow: 0 0 100px rgba(30, 60, 114, 0.8);
            animation: scaleIn 0.3s ease-out;
            position: relative;
            overflow: hidden;
        }

        .turn-call-content::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 10s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes scaleIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .turn-call-content .label {
            font-size: 2.5rem;
            text-transform: uppercase;
            letter-spacing: 5px;
            opacity: 0.9;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }

        .turn-call-content .ticket-number {
            font-size: 12rem;
            font-weight: 900;
            line-height: 1;
            text-shadow: 0 0 50px rgba(255,255,255,0.5);
            animation: pulse 1s ease-in-out infinite;
            position: relative;
            z-index: 1;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }

        .turn-call-content .window-info {
            font-size: 4rem;
            margin-top: 1.5rem;
            color: #ffc107;
            position: relative;
            z-index: 1;
        }

        .turn-call-content .window-info i {
            margin-right: 1rem;
            animation: bounce 1s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateX(0); }
            50% { transform: translateX(10px); }
        }

        .turn-call-content .countdown {
            position: absolute;
            top: 1.5rem;
            right: 2rem;
            font-size: 1.5rem;
            opacity: 0.6;
        }

        .turn-call-content .service-type {
            margin-top: 1.5rem;
            padding: 0.5rem 2rem;
            border-radius: 30px;
            font-size: 1.5rem;
            display: inline-block;
            position: relative;
            z-index: 1;
        }

        #videoContainer iframe {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 177.77vh;   /* 16:9 */
    height: 100vh;
    min-width: 100vw;
    min-height: 56.25vw;
    transform: translate(-50%, -50%);
}

/* Para hacer el logo más grande */
.logo-container img {
    max-width: 400px; /* aumentar */
    max-height: 250px; /* aumentar */
}

/* Para hacer el mensaje más grande */
.tv-message {
    font-size: 3.2rem; /* aumentar */
}
    </style>
</head>
<body>
    <div class="tv-container">
        <!-- Header Bar -->
        <div class="header-bar">
            <div class="business-name">
                <i class="fas fa-hospital mr-2"></i>{{ $settings['business_name'] }}
            </div>
            <div class="clock" id="clock">--:--:--</div>
        </div>

   <!-- Video/Content Area -->
<div class="video-area">
    <div id="videoContainer">
        @if(!empty($settings['tv_video_url']))
            @php
                $url = $settings['tv_video_url'];
            @endphp

            {{-- Video MP4 (local) - siempre visible de fondo --}}
            <video id="backgroundVideo" autoplay muted loop playsinline>
                <source src="{{ asset(ltrim($url, '/')) }}" type="video/mp4">
                Tu navegador no soporta el elemento de video.
            </video>
        @endif

        {{-- Overlay: Logo y Mensaje SIEMPRE visible --}}
        <div class="overlay-content">
            <div class="logo-container">
                @if(!empty($settings['tv_logo_url']))
                    <img src="{{ $settings['tv_logo_url'] }}" alt="Logo">
                @else
                    <i class="fas fa-hospital logo-placeholder"></i>
                @endif
            </div>

            @if(!empty($settings['tv_message']))
                <p class="tv-message">{{ $settings['tv_message'] }}</p>
            @else
                <p class="default-text">Bienvenido a {{ $settings['business_name'] ?? 'Nuestra Empresa' }}</p>
                <small class="default-text">Sistema de Turnos</small>
            @endif
        </div>
    </div>
</div>

        <!-- Windows Panel - Right Side -->
        <div class="windows-panel">
            <div class="windows-panel-title">
                <i class="fas fa-desktop mr-2"></i>Atendiendo
            </div>
            <div class="windows-grid" id="windowsGrid">
                <!-- Dynamic windows -->
            </div>
        </div>

        <!-- Queue Strip at Bottom -->
        <div class="queue-strip">
            <div class="queue-strip-title">
                <i class="fas fa-list-ol"></i>En Cola
            </div>
            <div class="queue-items" id="queueItems">
                <span class="no-queue-message">Cargando...</span>
            </div>
        </div>
    </div>

    <!-- Turn Call Modal -->
    <div class="turn-call-modal" id="turnCallModal">
        <div class="turn-call-content">
            <div class="countdown" id="modalCountdown">10</div>
            <div class="label">Turno Llamado</div>
            <div class="ticket-number" id="modalTicket">--</div>
            <div class="window-info">
                <i class="fas fa-arrow-right"></i>
                <span id="modalWindow">--</span>
            </div>
            <div class="service-type" id="modalService">--</div>
        </div>
    </div>

    <audio id="notificationSound" preload="auto" style="display:none;">
    <source src="https://assets.mixkit.co/sfx/preview/mixkit-bell-notification-933.mp3" type="audio/mpeg">
    </audio>

    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script>

            



        const REFRESH_RATE = {{ $settings['refresh_rate'] }};
        const SHOW_NEXT = {{ $settings['show_next'] }};
        const SOUND_ENABLED = {{ $settings['sound_enabled'] ? 'true' : 'false' }};
        const VOICE_ENABLED = {{ $settings['voice_enabled'] ? 'true' : 'false' }};
        const MODAL_DURATION = 10; // seconds

        let lastCalledTimestamp = 0;
        let modalTimeout = null;
        let countdownInterval = null;
        let lastProcessedCall = null;
        window.currentNotificationAudio = null;

        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent = now.toLocaleTimeString('es-ES');
        }

        function showTurnModal(ticketNumber, windowName, serviceColor, serviceName) {
            if (modalTimeout) clearTimeout(modalTimeout);
            if (countdownInterval) clearInterval(countdownInterval);

            $('#modalTicket').text(ticketNumber).css('color', serviceColor);
            $('#modalWindow').text(windowName);
            $('#modalService').text(serviceName).css('background-color', serviceColor);
            $('#modalCountdown').text(MODAL_DURATION);

            $('#turnCallModal').addClass('show');

            let remaining = MODAL_DURATION;
            countdownInterval = setInterval(function() {
                remaining--;
                $('#modalCountdown').text(remaining);
                if (remaining <= 0) {
                    clearInterval(countdownInterval);
                }
            }, 1000);

            modalTimeout = setTimeout(function() {
                $('#turnCallModal').removeClass('show');
                clearInterval(countdownInterval);
            }, MODAL_DURATION * 1000);
        }

        function playNotification(ticketNumber, windowName) {
            if (SOUND_ENABLED) {
                // Cancelar audio anterior
                if (window.currentNotificationAudio) {
                    window.currentNotificationAudio.pause();
                    window.currentNotificationAudio = null;
                }
                
                // Nueva instancia
                const audio = new Audio('https://assets.mixkit.co/sfx/preview/mixkit-bell-notification-933.mp3');
                audio.volume = 1;
                window.currentNotificationAudio = audio;
                
                audio.play().catch(err => console.log('Audio error:', err));
                
                audio.onended = () => {
                    window.currentNotificationAudio = null;
                };
            }

            if (VOICE_ENABLED && 'speechSynthesis' in window) {
                speechSynthesis.cancel();
                
                const utterance = new SpeechSynthesisUtterance(`Turno ${ticketNumber}, pasar a ${windowName}`);
                utterance.lang = 'es-ES';
                utterance.rate = 0.8;
                utterance.volume = 1;
                
                setTimeout(() => {
                    speechSynthesis.speak(utterance);
                }, 300);
            }
        }

        function fetchData() {
            $.get('{{ route("display.data") }}', { show_next: SHOW_NEXT })
                .done(function(data) {
                    // Queue items in bottom strip
                    let queueHtml = '';
                    if (data.pending.length === 0) {
                        queueHtml = '<span class="no-queue-message">No hay turnos en espera</span>';
                    } else {
                        data.pending.forEach(function(queue) {
                            queueHtml += `
                                <div class="queue-item">
                                    <span class="number" style="color: ${queue.service_color}">${queue.ticket_number}</span>
                                    <span class="service-badge" style="background-color: ${queue.service_color}">${queue.service_name}</span>
                                </div>
                            `;
                        });
                    }
                    $('#queueItems').html(queueHtml);

                    // Windows grid on right side
                    let windowsHtml = '';
                    data.windows.forEach(function(window) {
                        const isActive = window.current_ticket !== null;
                        windowsHtml += `
                            <div class="window-card ${isActive ? 'active' : 'inactive'}">
                                <div class="window-name">${window.name}</div>
                                <div class="window-ticket">${window.current_ticket || '-'}</div>
                            </div>
                        `;
                    });
                    $('#windowsGrid').html(windowsHtml);

                    // Check for new call - show modal
                    if (data.last_called && data.last_called.called_at > lastCalledTimestamp) {
                        lastCalledTimestamp = data.last_called.called_at;

                        playNotification(data.last_called.ticket_number, data.last_called.window);

                        showTurnModal(
                            data.last_called.ticket_number,
                            data.last_called.window,
                            data.last_called.service_color || '#1e3c72',
                            data.last_called.service_name || 'Servicio'
                        );
                    }
                });
        }

        // Initialize
        updateClock();
        setInterval(updateClock, 1000);
        fetchData();
        setInterval(fetchData, REFRESH_RATE);

        // Forzar loop del video (algunos navegadores no respetan el atributo loop)
        const bgVideo = document.getElementById('backgroundVideo');
        if (bgVideo) {
            bgVideo.addEventListener('ended', function() {
                this.currentTime = 0;
                this.play();
            });

            // Si el video se pausa por alguna razón, reiniciarlo
            bgVideo.addEventListener('pause', function() {
                if (!this.ended) {
                    this.play();
                }
            });

            // Intentar reproducir si está pausado al cargar
            bgVideo.addEventListener('loadeddata', function() {
                this.play().catch(e => console.log('Autoplay blocked:', e));
            });
        }

        // Limpiar al recargar
window.addEventListener('beforeunload', function() {
    if (window.currentNotificationAudio) {
        window.currentNotificationAudio.pause();
    }
    speechSynthesis.cancel();
});
    </script>
</body>
</html>
