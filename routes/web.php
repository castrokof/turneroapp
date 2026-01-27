<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ServiceTypeController;
use App\Http\Controllers\Admin\ServiceWindowController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Agent\DashboardController as AgentDashboardController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\DisplayController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Home - redirect to login or dashboard
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isAgent()) {
            return redirect()->route('agent.dashboard');
        }
        return redirect()->route('display.index');
    }
    return redirect()->route('login');
})->name('home');

// Authentication
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Public Display Routes
Route::prefix('display')->name('display.')->group(function () {
    Route::get('/', [DisplayController::class, 'index'])->name('index');
    Route::get('/tv', [DisplayController::class, 'tv'])->name('tv');
    Route::get('/service/{serviceType?}', [DisplayController::class, 'byService'])->name('service');
    Route::get('/kiosk', [DisplayController::class, 'kiosk'])->name('kiosk');
    Route::get('/data', [DisplayController::class, 'getData'])->name('data');
});

// Queue Generation (Kiosk)
Route::prefix('queue')->name('queue.')->group(function () {
    Route::get('/', [QueueController::class, 'index'])->name('index');
    Route::get('/create', [QueueController::class, 'create'])->name('create');
    Route::post('/', [QueueController::class, 'store'])->name('store');
    Route::get('/{queue}/ticket', [QueueController::class, 'ticket'])->name('ticket');
    Route::get('/{queue}/print', [QueueController::class, 'printTicket'])->name('print');
    Route::get('/{queue}/status', [QueueController::class, 'status'])->name('status');
    Route::post('/{queue}/cancel', [QueueController::class, 'cancel'])->name('cancel');
});

// Client search (public API for kiosk)
Route::get('/api/clients/search', [ClientController::class, 'search'])->name('api.clients.search');
Route::get('/api/clients/find', [ClientController::class, 'findByDocument'])->name('api.clients.find');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users Management
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Service Types
    Route::resource('service-types', ServiceTypeController::class);
    Route::post('/service-types/{serviceType}/toggle-status', [ServiceTypeController::class, 'toggleStatus'])->name('service-types.toggle-status');

    // Service Windows
    Route::resource('service-windows', ServiceWindowController::class);
    Route::post('/service-windows/{serviceWindow}/activate', [ServiceWindowController::class, 'activate'])->name('service-windows.activate');
    Route::post('/service-windows/{serviceWindow}/deactivate', [ServiceWindowController::class, 'deactivate'])->name('service-windows.deactivate');
    Route::post('/service-windows/{serviceWindow}/pause', [ServiceWindowController::class, 'pause'])->name('service-windows.pause');

    // Clients
    Route::resource('clients', ClientController::class);

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/initialize', [SettingsController::class, 'initializeDefaults'])->name('settings.initialize');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports', [ReportController::class, 'generate'])->name('reports.generate');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/chart', [ReportController::class, 'chartData'])->name('reports.chart');
});

/*
|--------------------------------------------------------------------------
| Agent Routes
|--------------------------------------------------------------------------
*/

Route::prefix('agent')->name('agent.')->middleware(['auth', 'role:admin,agent'])->group(function () {
    // Dashboard
    Route::get('/', [AgentDashboardController::class, 'index'])->name('dashboard');

    // Window Selection
    Route::post('/select-window', [AgentDashboardController::class, 'selectWindow'])->name('select-window');
    Route::post('/leave-window', [AgentDashboardController::class, 'leaveWindow'])->name('leave-window');
    Route::post('/pause-window', [AgentDashboardController::class, 'pauseWindow'])->name('pause-window');
    Route::post('/resume-window', [AgentDashboardController::class, 'resumeWindow'])->name('resume-window');

    // Queue Operations
    Route::post('/call-next', [AgentDashboardController::class, 'callNext'])->name('call-next');
    Route::post('/call/{queue}', [AgentDashboardController::class, 'callSpecific'])->name('call-specific');
    Route::post('/recall', [AgentDashboardController::class, 'recallCurrent'])->name('recall');
    Route::post('/start-service', [AgentDashboardController::class, 'startService'])->name('start-service');
    Route::post('/complete-service', [AgentDashboardController::class, 'completeService'])->name('complete-service');
    Route::post('/mark-absent', [AgentDashboardController::class, 'markAbsent'])->name('mark-absent');
    Route::post('/transfer', [AgentDashboardController::class, 'transferQueue'])->name('transfer');

    // API
    Route::get('/pending-queues', [AgentDashboardController::class, 'getPendingQueues'])->name('pending-queues');
    Route::get('/current-status', [AgentDashboardController::class, 'getCurrentStatus'])->name('current-status');
});


Route::get('/test-printer', function() {
    try {
        $printer = new \App\Services\PrinterService();
        
        $ticket = new \stdClass();
        $ticket->ticket_number = 'TEST-001';
        $ticket->serviceType = new \stdClass();
        $ticket->serviceType->name = 'Prueba de Impresion';
        $ticket->priority = 'normal';
        $ticket->serviceWindow = null;
        $ticket->pending_count = 5;
        
        $result = $printer->printTicket($ticket);
        
        return $result ? 'Ticket impreso correctamente!' : 'Error al imprimir';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});
