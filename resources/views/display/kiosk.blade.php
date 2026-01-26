<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Generar Turno - {{ $settings['business_name'] }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .kiosk-container {
            width: 100%;
            max-width: 900px;
            padding: 2rem;
        }
        .kiosk-header {
            text-align: center;
            color: #fff;
            margin-bottom: 2rem;
        }
        .kiosk-header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .kiosk-header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        .kiosk-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .kiosk-body {
            padding: 2rem;
        }
        .service-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        .service-btn {
            border: none;
            border-radius: 15px;
            padding: 2rem 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 150px;
        }
        .service-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .service-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        .service-btn .prefix {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .service-btn .name {
            font-size: 1.1rem;
        }
        .service-btn .time {
            font-size: 0.85rem;
            opacity: 0.8;
            margin-top: 0.5rem;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 0.5rem;
            font-weight: bold;
            transition: all 0.3s;
        }
        .step.active {
            background: #667eea;
            color: #fff;
        }
        .step.completed {
            background: #28a745;
            color: #fff;
        }
        .step-line {
            width: 50px;
            height: 3px;
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
        .priority-options {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 1rem;
        }
        .priority-btn {
            padding: 0.8rem 1.5rem;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            background: #fff;
            cursor: pointer;
            transition: all 0.2s;
        }
        .priority-btn:hover, .priority-btn.selected {
            border-color: #667eea;
            background: #f8f9fa;
        }
        .priority-btn i {
            margin-right: 0.5rem;
        }
        .ticket-result {
            text-align: center;
            padding: 2rem;
        }
        .ticket-result .ticket-number {
            font-size: 5rem;
            font-weight: bold;
            margin: 1rem 0;
            animation: pulse 1s infinite;
        }
        .ticket-result .service-name {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .ticket-result .info {
            font-size: 1.1rem;
            color: #6c757d;
        }
        .btn-back {
            position: absolute;
            top: 1rem;
            left: 1rem;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        .countdown {
            margin-top: 1rem;
            font-size: 1.2rem;
            color: #6c757d;
        }
        .countdown span {
            font-weight: bold;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="kiosk-container">
        <div class="kiosk-header">
            <h1><i class="fas fa-ticket-alt mr-3"></i>{{ $settings['business_name'] }}</h1>
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
                    <h3 class="text-center mb-4">Seleccione el Servicio</h3>
                    <div class="service-grid">
                        @foreach($serviceTypes as $service)
                            <button type="button" class="service-btn" style="background-color: {{ $service->color }}"
                                    onclick="selectService({{ $service->id }}, '{{ $service->name }}', '{{ $service->color }}')"
                                    {{ $service->isDailyLimitReached() ? 'disabled' : '' }}>
                                <span class="prefix">{{ $service->prefix }}</span>
                                <span class="name">{{ $service->name }}</span>
                                <span class="time"><i class="fas fa-clock mr-1"></i>~{{ $service->estimated_time }} min</span>
                                @if($service->isDailyLimitReached())
                                    <small class="mt-2"><i class="fas fa-ban"></i> Cupos agotados</small>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Step 2: Priority Selection -->
                <div id="step2">
                    <button class="btn btn-link btn-back" onclick="goToStep(1)">
                        <i class="fas fa-arrow-left"></i> Volver
                    </button>
                    <h3 class="text-center mb-4">¿Tiene alguna condición especial?</h3>
                    <p class="text-center text-muted">Seleccione si aplica para atención prioritaria</p>

                    <div class="priority-options">
                        <button type="button" class="priority-btn" onclick="selectPriority('normal')">
                            <i class="fas fa-user"></i> Atención Normal
                        </button>
                        <button type="button" class="priority-btn" onclick="selectPriority('priority', 'elderly')">
                            <i class="fas fa-user-clock text-info"></i> Adulto Mayor (60+)
                        </button>
                        <button type="button" class="priority-btn" onclick="selectPriority('priority', 'pregnant')">
                            <i class="fas fa-baby" style="color: #e83e8c;"></i> Embarazada
                        </button>
                        <button type="button" class="priority-btn" onclick="selectPriority('priority', 'disability')">
                            <i class="fas fa-wheelchair text-warning"></i> Discapacidad
                        </button>
                    </div>

                    <div class="text-center mt-4">
                        <small class="text-muted">Los turnos prioritarios tienen preferencia en la cola</small>
                    </div>
                </div>

                <!-- Step 3: Ticket Generated -->
                <div id="step3">
                    <div class="ticket-result">
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <h3>Su turno ha sido generado</h3>
                        <div class="ticket-number" id="ticketNumber" style="color: #667eea;">---</div>
                        <div class="service-name" id="serviceName">---</div>
                        <div class="info">
                            <p><i class="fas fa-users mr-2"></i>Personas delante de usted: <strong id="pendingCount">0</strong></p>
                            <p><i class="fas fa-clock mr-2"></i>Tiempo estimado: <strong id="estimatedTime">0</strong> minutos</p>
                        </div>
                        <hr>
                        <div class="countdown">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Regresando al inicio en <span id="countdownTimer">10</span> segundos...
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('display.index') }}" class="text-white">
                <i class="fas fa-tv mr-1"></i> Ver Pantalla de Turnos
            </a>
        </div>
    </div>

    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
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
        
        // Guardar contenido original del step 2
        const step2OriginalContent = $('#step2').html();

        function selectService(serviceId, serviceName, serviceColor) {
            selectedService = serviceId;
            selectedServiceName = serviceName;
            selectedServiceColor = serviceColor;
            goToStep(2);
        }

        function selectPriority(priority, condition = null) {
            // Mostrar mensaje de carga directamente sin reemplazar HTML
            $('#step2').html(`
                <div class="text-center" id="loadingMessage">
                    <i class="fas fa-spinner fa-3x fa-spin text-primary mb-3"></i>
                    <h4>Generando su turno...</h4>
                    <p class="text-muted">Por favor espere</p>
                </div>
            `);

            // Generate ticket
            $.post('{{ route("queue.store") }}', {
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
                    
                    // Pasar al step 3
                    goToStep(3);

                    // La impresión se hace automáticamente desde el servidor (PrinterService)
                    // No necesitamos imprimir desde el navegador

                    // Iniciar cuenta regresiva
                    startCountdown();
                }
            })
            .fail(function(xhr) {
                let message = xhr.responseJSON?.message || 'Error al generar el turno';
                
                // Restaurar contenido original del step 2
                $('#step2').html(step2OriginalContent);
                
                // Mostrar mensaje de error
                alert(message);
                
                // Volver al step 1 después de un delay
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
            
            // Si volvemos al step 2, restaurar contenido original
            if (step === 2) {
                $('#step2').html(step2OriginalContent);
            }
        }

        function printTicket() {
            if (generatedQueueId) {
                // Crear iframe oculto para impresión silenciosa
                const iframe = document.createElement('iframe');
                iframe.style.position = 'absolute';
                iframe.style.top = '-9999px';
                iframe.style.left = '-9999px';
                iframe.style.width = '80mm';
                iframe.style.height = '200mm';
                iframe.name = 'printFrame_' + Date.now();
                document.body.appendChild(iframe);

                // Cargar la página de impresión en el iframe
                iframe.src = `/queue/${generatedQueueId}/print`;

                // El iframe se imprimirá automáticamente (tiene window.print() en onload)
                // Remover el iframe después de un tiempo
                setTimeout(function() {
                    if (iframe.parentNode) {
                        document.body.removeChild(iframe);
                    }
                }, 5000);
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
                    newTicket();
                }
            }, 1000);
        }

        function newTicket() {
            selectedService = null;
            selectedServiceName = '';
            selectedServiceColor = '';
            generatedQueueId = null;
            
            // Limpiar datos del ticket
            $('#ticketNumber').text('---');
            $('#serviceName').text('---');
            $('#pendingCount').text('0');
            $('#estimatedTime').text('0');
            
            goToStep(1);
        }
    </script>
</body>
</html>