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
                        <button class="btn btn-primary btn-lg" onclick="printTicket()">
                            <i class="fas fa-print mr-2"></i>Imprimir Ticket
                        </button>
                        <button class="btn btn-outline-secondary btn-lg ml-2" onclick="newTicket()">
                            <i class="fas fa-redo mr-2"></i>Nuevo Turno
                        </button>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

        function selectService(serviceId, serviceName, serviceColor) {
            selectedService = serviceId;
            selectedServiceName = serviceName;
            selectedServiceColor = serviceColor;
            goToStep(2);
        }

        function selectPriority(priority, condition = null) {
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
                    goToStep(3);
                }
            })
            .fail(function(xhr) {
                alert(xhr.responseJSON?.message || 'Error al generar el turno');
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
        }

        function printTicket() {
            if (generatedQueueId) {
                window.open(`/queue/${generatedQueueId}/print`, '_blank', 'width=400,height=600');
            }
        }

        function newTicket() {
            selectedService = null;
            selectedServiceName = '';
            selectedServiceColor = '';
            generatedQueueId = null;
            goToStep(1);
        }
    </script>
</body>
</html>
