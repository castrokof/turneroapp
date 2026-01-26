<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket {{ $queue->ticket_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            width: 80mm;
            padding: 5mm;
        }
        .ticket {
            text-align: center;
        }
        .header {
            border-bottom: 2px dashed #000;
            padding-bottom: 3mm;
            margin-bottom: 3mm;
        }
        .business-name {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 2mm;
        }
        .ticket-number {
            font-size: 36pt;
            font-weight: bold;
            margin: 5mm 0;
            letter-spacing: 2mm;
        }
        .service-name {
            font-size: 12pt;
            margin-bottom: 3mm;
        }
        .info {
            text-align: left;
            margin: 3mm 0;
            font-size: 10pt;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1mm;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 3mm 0;
        }
        .footer {
            font-size: 9pt;
            margin-top: 3mm;
        }
        .priority-badge {
            display: inline-block;
            padding: 1mm 3mm;
            background: #000;
            color: #fff;
            font-size: 10pt;
            margin-top: 2mm;
        }
        @media print {
            body {
                width: 80mm;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <div class="business-name">{{ config('app.name') }}</div>
            <div>Sistema de Turnos</div>
        </div>

        <div class="service-name">{{ $queue->serviceType->name }}</div>
        <div class="ticket-number">{{ $queue->ticket_number }}</div>

        @if($queue->priority !== 'normal')
            <div class="priority-badge">{{ strtoupper($queue->priority_label) }}</div>
        @endif

        <div class="divider"></div>

        <div class="info">
            @if($queue->client)
                <div class="info-row">
                    <span>Cliente:</span>
                    <span>{{ $queue->client->full_name }}</span>
                </div>
            @endif
            <div class="info-row">
                <span>Fecha:</span>
                <span>{{ $queue->queue_date->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span>Hora:</span>
                <span>{{ $queue->created_at->format('H:i:s') }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <div class="footer">
            <p>Por favor espere a ser llamado</p>
            <p>Gracias por su visita</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
            Imprimir
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; margin-left: 10px;">
            Cerrar
        </button>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
