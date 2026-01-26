<?php

namespace App\Services;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Exception;
use Illuminate\Support\Facades\Log;

class PrinterService
{
    protected $printer;
    protected $connector;
    protected $os;

    public function __construct()
    {
        $this->os = PHP_OS_FAMILY;
        $this->connect();
    }

    protected function connect()
    {
        try {
            $profile = CapabilityProfile::load("simple");

            if ($this->os === 'Windows') {
                // Windows: impresora compartida por USB
                $printerName = config('printer.name', 'POS-80');
                $this->connector = new WindowsPrintConnector($printerName);
                Log::info('Conectando impresora Windows USB: ' . $printerName);
                
            } elseif ($this->os === 'Linux') {
                // Linux: puerto USB directo
                $usbPort = config('printer.usb_port', '/dev/usb/lp0');
                
                if (!file_exists($usbPort)) {
                    // Intentar con lp1 si lp0 no existe
                    $usbPort = '/dev/usb/lp1';
                }
                
                if (!file_exists($usbPort)) {
                    throw new Exception("Puerto USB no encontrado: {$usbPort}. Ejecuta 'ls -la /dev/usb/' para verificar.");
                }
                
                $this->connector = new FilePrintConnector($usbPort);
                Log::info('Conectando impresora Linux USB: ' . $usbPort);
                
            } else {
                throw new Exception('Sistema operativo no soportado: ' . $this->os);
            }

            $this->printer = new Printer($this->connector, $profile);
            Log::info('Impresora USB conectada exitosamente en ' . $this->os);
            
        } catch (Exception $e) {
            Log::error('Error conectando impresora USB: ' . $e->getMessage(), [
                'os' => $this->os,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Imprimir ticket térmico (diseño optimizado para POS-80 80mm)
     */
    public function printTicket($queue)
    {
        try {
            $printer = $this->printer;
            
            // ========================================
            // DISEÑO OPTIMIZADO PARA POS-80 (80mm)
            // ========================================
            
            // Logo / Nombre del negocio
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(2, 2);
            $printer->text(config('app.name', 'Sistema de Turnos') . "\n");
            $printer->setTextSize(1, 1);
            $printer->text(now()->format('d/m/Y') . '  ' . now()->format('H:i:s') . "\n");
            $printer->feed();
            
            // Línea separadora
            $printer->text("--------------------------------\n");
            
            // Servicio con ícono
            $printer->setEmphasis(true);
            $printer->setTextSize(1, 2);
            $printer->text(strtoupper($queue->serviceType->name) . "\n");
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
            if ($queue->priority !== 'normal') {
                $printer->setEmphasis(true);
                $printer->setTextSize(1, 2);
                $printer->text("  ⚠ PRIORITARIO ⚠\n");
                $printer->setTextSize(1, 1);
                $printer->setEmphasis(false);
                $printer->feed();
            }
            
            // Información adicional
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Ventanilla: " . ($queue->serviceWindow ? $queue->serviceWindow->name : 'Cualquiera') . "\n");
            $printer->text("Antes de usted: " . ($queue->pending_count ?? 0) . " personas\n");
            $printer->feed();
            
            // Mensaje final
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("================================\n");
            $printer->text("   ¡GRACIAS POR SU PACIENCIA!\n");
            $printer->text("   Espere visualización en TV\n");
            $printer->text("================================\n");
            $printer->feed(4); // Espacio extra para corte limpio
            
            // Cortar papel
            $printer->cut();
            
            // Cerrar conexión
            $printer->close();
            
            Log::info('Ticket impreso en POS-80 USB', [
                'ticket' => $queue->ticket_number,
                'service' => $queue->serviceType->name,
                'os' => $this->os
            ]);
            
            return true;
            
        } catch (Exception $e) {
            Log::error('Error imprimiendo en POS-80: ' . $e->getMessage(), [
                'ticket' => $queue->ticket_number ?? 'N/A',
                'os' => $this->os
            ]);
            return false;
        }
    }

    public function getOs()
    {
        return $this->os;
    }
}