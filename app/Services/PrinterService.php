<?php

namespace App\Services;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\CapabilityProfile;
use App\Models\SystemSetting;
use Exception;
use Illuminate\Support\Facades\Log;

class PrinterService
{
    protected $printer;
    protected $connector;
    protected $os;
    protected $enabled;

    public function __construct()
    {
        $this->os = PHP_OS_FAMILY;
        $this->enabled = config('printer.enabled', true);

        if ($this->enabled) {
            $this->connect();
        }
    }

    protected function connect()
    {
        try {
            $profile = CapabilityProfile::load("simple");

            if ($this->os === 'Windows') {
                // Windows: usar nombre de impresora instalada
                $printerName = config('printer.name', 'POS-80');

                // Intentar diferentes formatos de conexión
                try {
                    // Primero intentar conexión directa
                    $this->connector = new WindowsPrintConnector($printerName);
                } catch (Exception $e) {
                    // Si falla, intentar como impresora compartida local
                    $this->connector = new WindowsPrintConnector("smb://localhost/" . $printerName);
                }

                Log::info('Conectando impresora Windows: ' . $printerName);

            } elseif ($this->os === 'Linux') {
                // Linux: puerto USB directo
                $usbPort = config('printer.usb_port', '/dev/usb/lp0');

                // Buscar puerto disponible
                $ports = ['/dev/usb/lp0', '/dev/usb/lp1', '/dev/lp0', '/dev/lp1'];
                $foundPort = null;

                foreach ($ports as $port) {
                    if (file_exists($port)) {
                        $foundPort = $port;
                        break;
                    }
                }

                if (!$foundPort) {
                    throw new Exception("Puerto USB no encontrado. Ejecuta 'ls -la /dev/usb/' o 'ls -la /dev/lp*' para verificar.");
                }

                $usbPort = $foundPort;
                $this->connector = new FilePrintConnector($usbPort);
                Log::info('Conectando impresora Linux USB: ' . $usbPort);

            } else {
                throw new Exception('Sistema operativo no soportado: ' . $this->os);
            }

            $this->printer = new Printer($this->connector, $profile);
            Log::info('Impresora conectada exitosamente', ['os' => $this->os]);

        } catch (Exception $e) {
            Log::error('Error conectando impresora: ' . $e->getMessage(), [
                'os' => $this->os,
            ]);
            throw $e;
        }
    }

    /**
     * Imprimir ticket térmico (diseño optimizado para POS-80 80mm)
     */
    public function printTicket($queue)
    {
        // Si la impresión está deshabilitada, solo loguear
        if (!$this->enabled) {
            Log::info('Impresión deshabilitada - Ticket simulado', [
                'ticket' => $queue->ticket_number ?? 'N/A',
            ]);
            return true;
        }

        try {
            $printer = $this->printer;

            // ========================================
            // DISEÑO OPTIMIZADO PARA POS-80 (80mm)
            // ========================================

            // Logo / Nombre del negocio (desde configuración en BD)
            $businessName = SystemSetting::get('business_name', 'Sistema de Turnos');
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(2, 2);
            $printer->text($this->sanitizeText($businessName) . "\n");
            $printer->setTextSize(1, 1);
            $printer->text(now()->format('d/m/Y') . '  ' . now()->format('H:i:s') . "\n");
            $printer->feed();

            // Línea separadora
            $printer->text("--------------------------------\n");

            // Servicio
            $printer->setEmphasis(true);
            $printer->setTextSize(1, 2);
            $printer->text($this->sanitizeText(strtoupper($queue->serviceType->name)) . "\n");
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);
            $printer->text("--------------------------------\n");
            $printer->feed();

            // NÚMERO DE TICKET (GRANDE Y CENTRADO)
            $printer->setTextSize(4, 4);
            $printer->text($queue->ticket_number . "\n");
            $printer->setTextSize(1, 1);
            $printer->feed();

            // Prioridad (si aplica)
            if (isset($queue->priority) && $queue->priority !== 'normal') {
                $printer->setEmphasis(true);
                $printer->setTextSize(1, 2);
                $printer->text("*** PRIORITARIO ***\n");
                $printer->setTextSize(1, 1);
                $printer->setEmphasis(false);
                $printer->feed();
            }

            // Información adicional
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $ventanilla = isset($queue->serviceWindow) && $queue->serviceWindow ? $queue->serviceWindow->name : 'Cualquiera';
            $printer->text("Ventanilla: " . $ventanilla . "\n");
            $printer->text("Antes de usted: " . ($queue->pending_count ?? 0) . " personas\n");
            $printer->feed();

            // Mensaje final
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("================================\n");
            $printer->text("  GRACIAS POR SU PACIENCIA!\n");
            $printer->text("  Espere visualizacion en TV.\n");
            $printer->text("================================\n");
            $printer->feed(4);

            // Cortar papel
            $printer->cut();

            // Cerrar conexión
            $printer->close();

            Log::info('Ticket impreso correctamente', [
                'ticket' => $queue->ticket_number,
                'os' => $this->os
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('Error imprimiendo ticket: ' . $e->getMessage(), [
                'ticket' => $queue->ticket_number ?? 'N/A',
                'os' => $this->os
            ]);
            return false;
        }
    }

    /**
     * Sanitizar texto para impresora térmica (quitar caracteres especiales)
     */
    protected function sanitizeText($text)
    {
        // Reemplazar caracteres especiales que pueden causar problemas
        $replacements = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
            'ñ' => 'n', 'Ñ' => 'N',
            '¡' => '!', '¿' => '?',
            '⚠' => '*',
        ];

        return strtr($text, $replacements);
    }

    public function getOs()
    {
        return $this->os;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }
}
