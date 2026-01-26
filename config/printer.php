<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Impresora Térmica POS
    |--------------------------------------------------------------------------
    */

    // Habilitar/deshabilitar impresión (false para desarrollo sin impresora)
    'enabled' => env('PRINTER_ENABLED', true),

    // Windows: Nombre de la impresora instalada (ver en Panel de Control)
    'name' => env('PRINTER_NAME', 'POS-80'),

    // Linux: Puerto USB (/dev/usb/lp0, /dev/usb/lp1, /dev/lp0, etc.)
    'usb_port' => env('PRINTER_USB_PORT', '/dev/usb/lp0'),

    // Ancho del papel en caracteres (48 para 80mm, 32 para 58mm)
    'paper_width' => env('PRINTER_PAPER_WIDTH', 48),
];
