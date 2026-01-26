<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceTypeController extends Controller
{
    public function index()
    {
        $serviceTypes = ServiceType::withCount(['queues' => function ($q) {
            $q->whereDate('queue_date', today());
        }])->ordered()->get();

        return view('admin.service-types.index', compact('serviceTypes'));
    }

    public function create()
    {
        return view('admin.service-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prefix' => 'required|string|max:5|unique:service_types,prefix',
            'color' => 'required|string|max:7',
            'estimated_time' => 'required|integer|min:1|max:480',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'requires_appointment' => 'boolean',
            'daily_limit' => 'nullable|integer|min:1',
            'display_order' => 'integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['requires_appointment'] = $request->has('requires_appointment');
        $validated['prefix'] = strtoupper($validated['prefix']);

        $serviceType = ServiceType::create($validated);

        AuditLog::log('service_type_created', $serviceType, null, $serviceType->toArray());

        return redirect()->route('admin.service-types.index')
            ->with('success', 'Tipo de servicio creado correctamente.');
    }

    public function edit(ServiceType $serviceType)
    {
        return view('admin.service-types.edit', compact('serviceType'));
    }

    public function update(Request $request, ServiceType $serviceType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prefix' => ['required', 'string', 'max:5', Rule::unique('service_types')->ignore($serviceType->id)],
            'color' => 'required|string|max:7',
            'estimated_time' => 'required|integer|min:1|max:480',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'requires_appointment' => 'boolean',
            'daily_limit' => 'nullable|integer|min:1',
            'display_order' => 'integer|min:0',
        ]);

        $oldValues = $serviceType->toArray();

        $validated['is_active'] = $request->has('is_active');
        $validated['requires_appointment'] = $request->has('requires_appointment');
        $validated['prefix'] = strtoupper($validated['prefix']);

        $serviceType->update($validated);

        AuditLog::log('service_type_updated', $serviceType, $oldValues, $serviceType->toArray());

        return redirect()->route('admin.service-types.index')
            ->with('success', 'Tipo de servicio actualizado correctamente.');
    }

    public function destroy(ServiceType $serviceType)
    {
        if ($serviceType->queues()->whereDate('queue_date', today())->exists()) {
            return back()->with('error', 'No se puede eliminar un servicio con turnos activos hoy.');
        }

        AuditLog::log('service_type_deleted', $serviceType, $serviceType->toArray());

        $serviceType->delete();

        return redirect()->route('admin.service-types.index')
            ->with('success', 'Tipo de servicio eliminado correctamente.');
    }

    public function toggleStatus(ServiceType $serviceType)
    {
        $serviceType->update(['is_active' => !$serviceType->is_active]);

        AuditLog::log($serviceType->is_active ? 'service_type_activated' : 'service_type_deactivated', $serviceType);

        return $this->successResponse(
            $serviceType->is_active ? 'Servicio activado' : 'Servicio desactivado',
            ['is_active' => $serviceType->is_active]
        );
    }
}
