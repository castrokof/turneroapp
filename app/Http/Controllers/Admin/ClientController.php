<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('priority')) {
            $query->priority();
        }

        $clients = $query->orderBy('last_name')->orderBy('first_name')->paginate(15);

        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'document_type' => 'required|in:dni,passport,ce,ruc,other',
            'document_number' => 'required|string|max:20|unique:clients,document_number',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'is_elderly' => 'boolean',
            'is_pregnant' => 'boolean',
            'has_disability' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['is_elderly'] = $request->has('is_elderly');
        $validated['is_pregnant'] = $request->has('is_pregnant');
        $validated['has_disability'] = $request->has('has_disability');

        $client = Client::create($validated);

        AuditLog::log('client_created', $client, null, $client->toArray());

        if ($request->ajax()) {
            return $this->successResponse('Cliente registrado correctamente.', $client);
        }

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente registrado correctamente.');
    }

    public function show(Client $client)
    {
        $history = $client->getQueueHistory(20);

        return view('admin.clients.show', compact('client', 'history'));
    }

    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'document_type' => 'required|in:dni,passport,ce,ruc,other',
            'document_number' => ['required', 'string', 'max:20', Rule::unique('clients')->ignore($client->id)],
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'is_elderly' => 'boolean',
            'is_pregnant' => 'boolean',
            'has_disability' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $oldValues = $client->toArray();

        $validated['is_elderly'] = $request->has('is_elderly');
        $validated['is_pregnant'] = $request->has('is_pregnant');
        $validated['has_disability'] = $request->has('has_disability');

        $client->update($validated);

        AuditLog::log('client_updated', $client, $oldValues, $client->toArray());

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Client $client)
    {
        AuditLog::log('client_deleted', $client, $client->toArray());

        $client->delete();

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }

    public function search(Request $request)
    {
        $term = $request->get('term');

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $clients = Client::search($term)
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'document_type', 'document_number', 'is_elderly', 'is_pregnant', 'has_disability']);

        return response()->json($clients->map(function ($client) {
            return [
                'id' => $client->id,
                'text' => $client->full_name . ' - ' . $client->document_display,
                'full_name' => $client->full_name,
                'document' => $client->document_display,
                'has_priority' => $client->hasPriority(),
            ];
        }));
    }

    public function findByDocument(Request $request)
    {
        $document = $request->get('document');

        $client = Client::where('document_number', $document)->first();

        if (!$client) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'client' => [
                'id' => $client->id,
                'full_name' => $client->full_name,
                'document' => $client->document_display,
                'has_priority' => $client->hasPriority(),
                'is_elderly' => $client->is_elderly,
                'is_pregnant' => $client->is_pregnant,
                'has_disability' => $client->has_disability,
            ],
        ]);
    }
}
