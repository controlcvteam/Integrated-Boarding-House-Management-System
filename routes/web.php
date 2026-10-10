<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MaintenanceController as AdminMaintenanceController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\PendingTenantController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\RoomController as AdminRoomController;
use App\Http\Controllers\Admin\RoomRequestController as AdminRoomRequestController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\TenantController as AdminTenantController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PublicRoomController;
use App\Http\Controllers\Tenant\DashboardController as TenantDashboardController;
use App\Http\Controllers\Tenant\MaintenanceController as TenantMaintenanceController;
use App\Http\Controllers\Tenant\MyRoomController as TenantMyRoomController;
use App\Http\Controllers\Tenant\PaymentController as TenantPaymentController;
use App\Http\Controllers\Tenant\RoomController as TenantRoomController;
use App\Http\Controllers\Tenant\SettingsController as TenantSettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Dynamic Storage & Media Route (Serves DB-stored media & local assets)
Route::get('/storage/{path}', [MediaController::class, 'serve'])
    ->where('path', '.*')
    ->name('media.serve');

// Root
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->isApprovedTenant()) {
            return redirect()->route('tenant.dashboard');
        } elseif (auth()->user()->isRejected()) {
            return redirect()->route('auth.rejected');
        } else {
            return redirect()->route('auth.pending');
        }
    }
    return redirect()->route('login');
});

// Public Room Browsing
Route::get('/rooms', [PublicRoomController::class, 'index'])->name('rooms.public');
Route::get('/public-rooms', [PublicRoomController::class, 'index'])->name('public.rooms');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Pending & Rejected Account Status Routes (Requires Auth)
Route::middleware('auth')->group(function () {
    Route::get('/account/pending', [LoginController::class, 'pending'])->name('account.pending');
    Route::get('/account/rejected', [LoginController::class, 'rejected'])->name('account.rejected');
    Route::get('/auth/pending', [LoginController::class, 'pending'])->name('auth.pending');
    Route::get('/auth/rejected', [LoginController::class, 'rejected'])->name('auth.rejected');

    // Notifications
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsReadAndRedirect'])->name('notifications.read');
    Route::post('/notifications/{id}/read-single', [NotificationController::class, 'markAsRead'])->name('notifications.read-single');
    Route::post('/notifications/{id}/unread', [NotificationController::class, 'markAsUnread'])->name('notifications.unread');
    Route::delete('/notifications/delete-all', [NotificationController::class, 'destroyAll'])->name('notifications.destroy-all');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// Admin / Landlady Routes
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Pending Registrations
        Route::get('/pending-tenants', [PendingTenantController::class, 'index'])->name('pending-tenants.index');
        Route::get('/pending-tenants/{id}', [PendingTenantController::class, 'show'])->name('pending-tenants.show');
        Route::post('/pending-tenants/{id}/approve', [PendingTenantController::class, 'approve'])->name('pending-tenants.approve');
        Route::post('/pending-tenants/{id}/reject', [PendingTenantController::class, 'reject'])->name('pending-tenants.reject');

        // Room Requests
        Route::get('/room-requests', [AdminRoomRequestController::class, 'index'])->name('room-requests.index');
        Route::post('/room-requests/{id}/approve', [AdminRoomRequestController::class, 'approve'])->name('room-requests.approve');
        Route::post('/room-requests/{id}/reject', [AdminRoomRequestController::class, 'reject'])->name('room-requests.reject');
        Route::delete('/room-requests/{id}', [AdminRoomRequestController::class, 'destroy'])->name('room-requests.destroy');

        // Tenant Management
        Route::get('/tenants', [AdminTenantController::class, 'index'])->name('tenants.index');
        Route::get('/tenants/create', [AdminTenantController::class, 'create'])->name('tenants.create');
        Route::post('/tenants', [AdminTenantController::class, 'store'])->name('tenants.store');
        Route::get('/tenants/{id}', [AdminTenantController::class, 'show'])->name('tenants.show');
        Route::get('/tenants/{id}/edit', [AdminTenantController::class, 'edit'])->name('tenants.edit');
        Route::put('/tenants/{id}', [AdminTenantController::class, 'update'])->name('tenants.update');
        Route::delete('/tenants/{id}', [AdminTenantController::class, 'destroy'])->name('tenants.destroy');
        Route::post('/tenants/{tenant}/transfer-room', [AdminTenantController::class, 'transferRoom'])->name('tenants.transfer');
        Route::post('/tenants/{tenant}/transfer', [AdminTenantController::class, 'transferRoom'])->name('tenants.transfer-room');
        Route::post('/tenants/{tenant}/move-out', [AdminTenantController::class, 'markMovedOut'])->name('tenants.move-out');
        Route::post('/tenants/{tenant}/mark-moved-out', [AdminTenantController::class, 'markMovedOut'])->name('tenants.mark-moved-out');

        // Rooms Management
        Route::get('/rooms', [AdminRoomController::class, 'index'])->name('rooms.index');
        Route::get('/rooms/create', [AdminRoomController::class, 'create'])->name('rooms.create');
        Route::post('/rooms', [AdminRoomController::class, 'store'])->name('rooms.store');
        Route::get('/rooms/{id}', [AdminRoomController::class, 'show'])->name('rooms.show');
        Route::get('/rooms/{id}/edit', [AdminRoomController::class, 'edit'])->name('rooms.edit');
        Route::put('/rooms/{id}', [AdminRoomController::class, 'update'])->name('rooms.update');
        Route::delete('/rooms/{id}', [AdminRoomController::class, 'destroy'])->name('rooms.destroy');
        Route::post('/rooms/{id}/toggle-availability', [AdminRoomController::class, 'toggleAvailability'])->name('rooms.toggle-availability');
        Route::post('/rooms/{id}/images', [AdminRoomController::class, 'uploadImages'])->name('rooms.upload-images');
        Route::post('/rooms/{room}/images/{image}/set-primary', [AdminRoomController::class, 'setPrimaryImage'])->name('rooms.set-primary');
        Route::post('/rooms/images/{imageId}/set-primary', [AdminRoomController::class, 'setPrimaryImage'])->name('rooms.set-primary-image');
        Route::delete('/rooms/{room}/images/{image}', [AdminRoomController::class, 'deleteImage'])->name('rooms.delete-image');
        Route::delete('/rooms/images/{imageId}', [AdminRoomController::class, 'deleteImage'])->name('rooms.delete-image-legacy');

        // Payments (Cash & GCash)
        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/create', [AdminPaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [AdminPaymentController::class, 'store'])->name('payments.store');
        Route::post('/payments/quick-cash', [AdminPaymentController::class, 'store'])->name('payments.record-cash-quick');
        Route::get('/payments/{id}', [AdminPaymentController::class, 'show'])->name('payments.show');
        Route::get('/payments/{id}/edit', [AdminPaymentController::class, 'edit'])->name('payments.edit');
        Route::put('/payments/{id}', [AdminPaymentController::class, 'update'])->name('payments.update');
        Route::get('/payments/{id}/edit-history', [AdminPaymentController::class, 'editHistoryData'])->name('payments.edit-history');
        Route::get('/payments/{id}/history', [AdminPaymentController::class, 'historyPage'])->name('payments.history');
        Route::delete('/payments/{id}', [AdminPaymentController::class, 'destroy'])->name('payments.destroy');
        Route::post('/payments/{id}/verify', [AdminPaymentController::class, 'verifyGcash'])->name('payments.verify');
        Route::post('/payments/{id}/approve', [AdminPaymentController::class, 'approveGcash'])->name('payments.approve');
        Route::post('/payments/{id}/approve-gcash', [AdminPaymentController::class, 'approveGcash'])->name('payments.approve-gcash');
        Route::post('/payments/{id}/reject', [AdminPaymentController::class, 'rejectGcash'])->name('payments.reject');
        Route::post('/payments/{id}/reject-gcash', [AdminPaymentController::class, 'rejectGcash'])->name('payments.reject-gcash');
        Route::get('/payments/{id}/receipt', [AdminPaymentController::class, 'receipt'])->name('payments.receipt');

        // Maintenance Management
        Route::get('/maintenance', [AdminMaintenanceController::class, 'index'])->name('maintenance.index');
        Route::get('/maintenance/create', [AdminMaintenanceController::class, 'create'])->name('maintenance.create');
        Route::post('/maintenance', [AdminMaintenanceController::class, 'store'])->name('maintenance.store');
        Route::get('/maintenance/{id}', [AdminMaintenanceController::class, 'show'])->name('maintenance.show');
        Route::get('/maintenance/{id}/edit', [AdminMaintenanceController::class, 'edit'])->name('maintenance.edit');
        Route::put('/maintenance/{id}', [AdminMaintenanceController::class, 'update'])->name('maintenance.update');
        Route::delete('/maintenance/{id}', [AdminMaintenanceController::class, 'destroy'])->name('maintenance.destroy');

        // Reports
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');

        // Settings
        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings/profile', [AdminSettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::put('/settings/password', [AdminSettingsController::class, 'updatePassword'])->name('settings.password');
        Route::put('/settings/gcash', [AdminSettingsController::class, 'updateGcash'])->name('settings.gcash');
    });

// Tenant Routes (Requires Approved Tenant)
Route::prefix('tenant')
    ->name('tenant.')
    ->middleware(['auth', 'tenant', 'tenant.approved'])
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [TenantDashboardController::class, 'index'])->name('dashboard');

        // Browse Available Rooms
        Route::get('/rooms', [TenantRoomController::class, 'index'])->name('rooms.index');
        Route::get('/rooms/{id}', [TenantRoomController::class, 'show'])->name('rooms.show');
        Route::post('/rooms/{id}/request', [TenantRoomController::class, 'requestRoom'])->name('rooms.request');

        // My Assigned Room
        Route::get('/my-room', [TenantMyRoomController::class, 'index'])->name('my-room');
        Route::get('/my-room/view', [TenantMyRoomController::class, 'index'])->name('my-room.index');

        // Rent Payments (GCash submission & history)
        Route::get('/payments', [TenantPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/submit-gcash', [TenantPaymentController::class, 'submitGcashForm'])->name('payments.submit');
        Route::post('/payments/submit-gcash', [TenantPaymentController::class, 'storeGcash'])->name('payments.store');
        Route::get('/payments/{id}/receipt', [TenantPaymentController::class, 'showReceipt'])->name('payments.receipt');
        Route::get('/payments/{id}/edit-history', [TenantPaymentController::class, 'editHistoryData'])->name('payments.edit-history');
        Route::get('/payments/{id}/history', [TenantPaymentController::class, 'historyPage'])->name('payments.history');

        // Maintenance Requests
        Route::get('/maintenance', [TenantMaintenanceController::class, 'index'])->name('maintenance.index');
        Route::get('/maintenance/create', [TenantMaintenanceController::class, 'create'])->name('maintenance.create');
        Route::post('/maintenance', [TenantMaintenanceController::class, 'store'])->name('maintenance.store');
        Route::get('/maintenance/{id}', [TenantMaintenanceController::class, 'show'])->name('maintenance.show');
        Route::get('/maintenance/{id}/edit', [TenantMaintenanceController::class, 'edit'])->name('maintenance.edit');
        Route::put('/maintenance/{id}', [TenantMaintenanceController::class, 'update'])->name('maintenance.update');
        Route::delete('/maintenance/{id}', [TenantMaintenanceController::class, 'destroy'])->name('maintenance.destroy');

        // Settings & Emergency Contacts
        Route::get('/settings', [TenantSettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings/profile', [TenantSettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::put('/settings/password', [TenantSettingsController::class, 'updatePassword'])->name('settings.password');
    });
