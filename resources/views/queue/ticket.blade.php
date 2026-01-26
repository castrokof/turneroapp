<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turno {{ $queue->ticket_number }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .ticket-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 400px;
            width: 100%;
            overflow: hidden;
        }
        .ticket-header {
            padding: 2rem;
            text-align: center;
            color: #fff;
        }
        .ticket-number {
            font-size: 5rem;
            font-weight: bold;
            margin: 1rem 0;
        }
        .ticket-body {
            padding: 2rem;
            text-align: center;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #6c757d;
        }
        .info-value {
            font-weight: 600;
        }
        .status-badge {
            font-size: 1.2rem;
            padding: 0.5rem 1.5rem;
        }
        .btn-actions {
            padding: 1.5rem;
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="ticket-card">
        <div class="ticket-header" style="background-color: {{ $queue->serviceType->color }}">
            <h3>{{ $queue->serviceType->name }}</h3>
            <div class="ticket-number">{{ $queue->ticket_number }}</div>
            <span class="badge badge-light status-badge">{{ $queue->status_label }}</span>
        </div>

        <div class="ticket-body">
            @if($queue->client)
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-user mr-2"></i>Cliente</span>
                    <span class="info-value">{{ $queue->client->full_name }}</span>
                </div>
            @endif

            <div class="info-row">
                <span class="info-label"><i class="fas fa-calendar mr-2"></i>Fecha</span>
                <span class="info-value">{{ $queue->queue_date->format('d/m/Y') }}</span>
            </div>

            <div class="info-row">
                <span class="info-label"><i class="fas fa-clock mr-2"></i>Hora</span>
                <span class="info-value">{{ $queue->created_at->format('H:i:s') }}</span>
            </div>

            @if($queue->priority !== 'normal')
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-star mr-2"></i>Prioridad</span>
                    <span class="info-value">
                        <span class="badge badge-{{ $queue->priority_color }}">{{ $queue->priority_label }}</span>
                    </span>
                </div>
            @endif

            <div class="info-row">
                <span class="info-label"><i class="fas fa-users mr-2"></i>Delante de usted</span>
                <span class="info-value">{{ $pendingCount }} personas</span>
            </div>

            <div class="info-row">
                <span class="info-label"><i class="fas fa-hourglass-half mr-2"></i>Tiempo estimado</span>
                <span class="info-value">~{{ $estimatedWait }} minutos</span>
            </div>

            @if($queue->serviceWindow)
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-door-open mr-2"></i>Ventanilla</span>
                    <span class="info-value">{{ $queue->serviceWindow->name }}</span>
                </div>
            @endif
        </div>

        <div class="btn-actions text-center">
            <a href="{{ route('queue.print', $queue) }}" class="btn btn-primary" target="_blank">
                <i class="fas fa-print mr-1"></i> Imprimir
            </a>
            <a href="{{ route('display.kiosk') }}" class="btn btn-outline-secondary">
                <i class="fas fa-plus mr-1"></i> Nuevo Turno
            </a>
        </div>
    </div>

    <script>
        // Auto-refresh status every 10 seconds
        setInterval(function() {
            fetch('{{ route("queue.status", $queue) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.status !== '{{ $queue->status }}') {
                        location.reload();
                    }
                });
        }, 10000);
    </script>
</body>
</html>
