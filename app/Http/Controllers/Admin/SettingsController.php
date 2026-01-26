<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::all()->groupBy('group');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            $setting = SystemSetting::where('key', $key)->first();

            if ($setting) {
                $oldValue = $setting->value;
                $setting->update(['value' => $value]);

                if ($oldValue !== $value) {
                    AuditLog::log('setting_updated', $setting, ['value' => $oldValue], ['value' => $value]);
                }
            }
        }

        // Clear settings cache
        cache()->forget('settings');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Configuración actualizada correctamente.');
    }

    public function initializeDefaults()
    {
        $defaults = [
            // General
            ['key' => 'business_name', 'value' => 'Sistema de Turnos', 'type' => 'string', 'group' => 'general', 'description' => 'Nombre del negocio'],
            ['key' => 'business_address', 'value' => '', 'type' => 'string', 'group' => 'general', 'description' => 'Dirección del negocio'],
            ['key' => 'business_phone', 'value' => '', 'type' => 'string', 'group' => 'general', 'description' => 'Teléfono del negocio'],

            // Schedule
            ['key' => 'opening_time', 'value' => '08:00', 'type' => 'string', 'group' => 'schedule', 'description' => 'Hora de apertura'],
            ['key' => 'closing_time', 'value' => '17:00', 'type' => 'string', 'group' => 'schedule', 'description' => 'Hora de cierre'],
            ['key' => 'work_days', 'value' => '["monday","tuesday","wednesday","thursday","friday"]', 'type' => 'json', 'group' => 'schedule', 'description' => 'Días laborales'],

            // Queue
            ['key' => 'max_wait_time', 'value' => '30', 'type' => 'integer', 'group' => 'queue', 'description' => 'Tiempo máximo de espera (minutos)'],
            ['key' => 'absent_timeout', 'value' => '3', 'type' => 'integer', 'group' => 'queue', 'description' => 'Tiempo para marcar ausente (minutos)'],
            ['key' => 'max_recalls', 'value' => '3', 'type' => 'integer', 'group' => 'queue', 'description' => 'Máximo de rellamados'],
            ['key' => 'auto_reset_daily', 'value' => '1', 'type' => 'boolean', 'group' => 'queue', 'description' => 'Reiniciar contadores diariamente'],

            // Display
            ['key' => 'display_refresh_rate', 'value' => '5', 'type' => 'integer', 'group' => 'display', 'description' => 'Frecuencia de actualización (segundos)'],
            ['key' => 'display_show_next', 'value' => '5', 'type' => 'integer', 'group' => 'display', 'description' => 'Cantidad de turnos siguientes a mostrar'],
            ['key' => 'display_sound_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'display', 'description' => 'Sonido habilitado'],
            ['key' => 'display_voice_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'display', 'description' => 'Voz habilitada'],

            // Demo
            ['key' => 'demo_mode', 'value' => '0', 'type' => 'boolean', 'group' => 'system', 'description' => 'Modo demo'],
        ];

        foreach ($defaults as $default) {
            SystemSetting::firstOrCreate(
                ['key' => $default['key']],
                $default
            );
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Configuración inicializada correctamente.');
    }
}
