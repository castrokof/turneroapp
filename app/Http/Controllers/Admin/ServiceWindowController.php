<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ServiceWindow;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceWindowController extends Controller
{
    public function index()
    {
        $windows = ServiceWindow::with(['currentAgent', 'serviceTypes'])
            ->withCount(['queues' => function ($q) {
                $q->whereDate('queue_date', today())->where('status', 'completed');
            }])
            ->ordered()
            ->get();

        return view('admin.service-windows.index', compact('windows'));
    }

    public function create()
    {
        $serviceTypes = ServiceType::active()->ordered()->get();
        $agents = User::agents()->active()->get();

        return view('admin.service-windows.create', compact('serviceTypes', 'agents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:service_windows,code',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'display_order' => 'integer|min:0',
            'service_types' => 'array',
            'service_types.*' => 'exists:service_types,id',
        ]);

        $validated['code'] = strtoupper($validated['code']);

        $window = ServiceWindow::create($validated);

        if (!empty($validated['service_types'])) {
            $window->serviceTypes()->sync($validated['service_types']);
        }

        AuditLog::log('service_window_created', $window, null, $window->toArray());

        return redirect()->route('admin.service-windows.index')
            ->with('success', 'Ventanilla creada correctamente.');
    }

    public function edit(ServiceWindow $serviceWindow)
    {
        $serviceTypes = ServiceType::active()->ordered()->get();
        $agents = User::agents()->active()->get();
        $selectedServices = $serviceWindow->serviceTypes->pluck('id')->toArray();

        return view('admin.service-windows.edit', compact('serviceWindow', 'serviceTypes', 'agents', 'selectedServices'));
    }

    public function update(Request $request, ServiceWindow $serviceWindow)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:10', Rule::unique('service_windows')->ignore($serviceWindow->id)],
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'display_order' => 'integer|min:0',
            'service_types' => 'array',
            'service_types.*' => 'exists:service_types,id',
        ]);

        $oldValues = $serviceWindow->toArray();

        $validated['code'] = strtoupper($validated['code']);

        $serviceWindow->update($validated);

        $serviceWindow->serviceTypes()->sync($validated['service_types'] ?? []);

        AuditLog::log('service_window_updated', $serviceWindow, $oldValues, $serviceWindow->toArray());

        return redirect()->route('admin.service-windows.index')
            ->with('success', 'Ventanilla actualizada correctamente.');
    }

    public function destroy(ServiceWindow $serviceWindow)
    {
        if ($serviceWindow->status === 'active') {
            return back()->with('error', 'No se puede eliminar una ventanilla activa.');
        }

        AuditLog::log('service_window_deleted', $serviceWindow, $serviceWindow->toArray());

        $serviceWindow->delete();

        return redirect()->route('admin.service-windows.index')
            ->with('success', 'Ventanilla eliminada correctamente.');
    }

    public function activate(Request $request, ServiceWindow $serviceWindow)
    {
        $validated = $request->validate([
            'agent_id' => 'required|exists:users,id',
        ]);

        $serviceWindow->activate($validated['agent_id']);

        AuditLog::log('service_window_activated', $serviceWindow);

        return $this->successResponse('Ventanilla activada correctamente.');
    }

    public function deactivate(ServiceWindow $serviceWindow)
    {
        $currentQueue = $serviceWindow->getCurrentQueue();

        if ($currentQueue) {
            return $this->errorResponse('No se puede desactivar la ventanilla mientras atiende un turno.');
        }

        $serviceWindow->deactivate();

        AuditLog::log('service_window_deactivated', $serviceWindow);

        return $this->successResponse('Ventanilla desactivada correctamente.');
    }

    public function pause(ServiceWindow $serviceWindow)
    {
        $serviceWindow->pause();

        AuditLog::log('service_window_paused', $serviceWindow);

        return $this->successResponse('Ventanilla pausada correctamente.');
    }
}
