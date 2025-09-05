<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentApprovalController;
use App\Http\Controllers\DocumentAuditLogController;
use App\Http\Controllers\ComplianceTemplateController;
use App\Http\Controllers\ComplianceReportController;
use App\Http\Controllers\UserController;


Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified', 'department'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    });

    Route::prefix('compliance-templates')->group(function () {
        Route::get('/', [ComplianceTemplateController::class, 'index'])
            ->name('compliance.templates.index');

        Route::get('/create', [ComplianceTemplateController::class, 'create'])
            ->name('compliance.templates.create');

        Route::post('/', [ComplianceTemplateController::class, 'store'])
            ->name('compliance.templates.store');

        Route::get('/field-row', [ComplianceTemplateController::class, 'getFieldRow'])
            ->name('compliance.templates.field-row');

        Route::get('/{compliance_template}', [ComplianceTemplateController::class, 'show'])
            ->name('compliance.templates.show')
            ->middleware('can:view,compliance_template');

        Route::get('/{compliance_template}/edit', [ComplianceTemplateController::class, 'edit'])
            ->name('compliance.templates.edit')
            ->middleware('can:update,compliance_template');

        Route::put('/{compliance_template}', [ComplianceTemplateController::class, 'update'])
            ->name('compliance.templates.update')
            ->middleware('can:update,compliance_template');

        Route::delete('/{compliance_template}', [ComplianceTemplateController::class, 'destroy'])
            ->name('compliance.templates.destroy')
            ->middleware('can:delete,compliance_template');
    });


    // Compliance Reports
    // Compliance Reports
    Route::prefix('compliance-reports')->name('compliance.reports.')->group(function () {
        // List all reports
        Route::get('/', [ComplianceReportController::class, 'index'])->name('index');

        // Show template selection
        Route::get('/create', [ComplianceReportController::class, 'create'])->name('create');

        // Show form for specific template
        Route::get('/create/{template}', [ComplianceReportController::class, 'createFromTemplate'])
            ->name('create-from-template');

        // Store new report
        Route::post('/create/{template}', [ComplianceReportController::class, 'store'])
            ->name('store');

        // View report
        Route::get('/{report}', [ComplianceReportController::class, 'show'])->name('show');

        // Edit report
        Route::get('/{report}/edit', [ComplianceReportController::class, 'edit'])->name('edit');

        // Update report
        Route::put('/{report}', [ComplianceReportController::class, 'update'])->name('update');

        // Delete report
        Route::delete('/{report}', [ComplianceReportController::class, 'destroy'])->name('destroy');

        // Submit for approval
        Route::post('/{report}/submit', [ComplianceReportController::class, 'submit'])->name('submit');

        // Approve report
        Route::post('/{report}/approve', [ComplianceReportController::class, 'approve'])->name('approve');

        // Reject report
        Route::post('/{report}/reject', [ComplianceReportController::class, 'reject'])->name('reject');

        Route::get('/create/{template}', [ComplianceReportController::class, 'createFromTemplate'])
            ->name('create-from-template'); // Changed from create-with-template


    });



    // Document Management Routes (existing - unchanged)
    Route::prefix('documents')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('documents.index');
        Route::get('/create', [DocumentController::class, 'create'])->name('documents.create');
        Route::post('/', [DocumentController::class, 'store'])->name('documents.store');

        Route::get('/trash', [DocumentController::class, 'trash'])
            ->name('documents.trash')
            ->middleware('can:viewTrash,App\Models\Document');

        Route::post('/{document}/restore', [DocumentController::class, 'restore'])
            ->name('documents.restore')
            ->middleware('can:restore,document');

        Route::delete('/{id}/force-delete', [DocumentController::class, 'forceDelete'])
            ->name('documents.force-delete')
            ->middleware('can:forceDelete,App\Models\Document');

        Route::get('/{document}', [DocumentController::class, 'show'])->name('documents.show');
        Route::get('/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
        Route::put('/{document}', [DocumentController::class, 'update'])->name('documents.update');
        Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

        Route::post('/{document}/submit', [DocumentController::class, 'submitForApproval'])
            ->name('documents.submit')
            ->middleware('can:submitForApproval,document');

        Route::get('/{document}/download', [DocumentController::class, 'download'])
            ->name('documents.download')
            ->middleware('can:download,document');

        Route::get('/{document}/preview', [DocumentController::class, 'preview'])
            ->name('documents.preview')
            ->middleware('can:download,document');

        Route::get('/{document}/versions', [DocumentController::class, 'showVersions'])
            ->name('documents.versions')
            ->middleware('can:view,document');

        Route::get('/{document}/create-version', [DocumentController::class, 'showCreateVersionForm'])
            ->name('documents.create-version')
            ->middleware('can:create,App\Models\Document');

        Route::post('/{document}/create-version', [DocumentController::class, 'createNewVersion'])
            ->name('documents.store-version')
            ->middleware('can:create,App\Models\Document');
    });


    Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'can:admin_access'])->group(function () {

        // User Management
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('users/{user}/edit', [UserController::class, 'editAdmin'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'updateAdmin'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Role Management
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)->except(['show', 'create', 'edit']);

        // Permission Management
        Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class)->except(['show']);

        // Department Management
        Route::resource('departments', \App\Http\Controllers\Admin\DepartmentController::class);

        // Location Management
        Route::resource('locations', \App\Http\Controllers\Admin\LocationController::class);

        //  Updated System Settings
        Route::prefix('settings')->name('settings.')->middleware(['can:settings_manage'])->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SystemSettingController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\SystemSettingController::class, 'update'])->name('update');
        });
    });


    // Document Approval Routes (existing - unchanged)
    Route::prefix('approvals')->group(function () {
        Route::get('/', [DocumentApprovalController::class, 'index'])
            ->name('approvals.index')
            ->middleware('can:document_approve');

        Route::get('/history', [DocumentApprovalController::class, 'history'])
            ->name('approvals.history')
            ->middleware('can:viewApprovalHistory,App\Models\Document');

        Route::post('/{document}/approve', [DocumentApprovalController::class, 'approve'])
            ->name('approvals.approve')
            ->middleware('can:approve,document');

        Route::post('/{document}/reject', [DocumentApprovalController::class, 'reject'])
            ->name('approvals.reject')
            ->middleware('can:reject,document');
    });

    // Audit Logs (existing - unchanged)
    Route::prefix('audit-logs')->group(function () {
        Route::get('/', [DocumentAuditLogController::class, 'index'])
            ->name('audit-logs.index')
            ->middleware('can:viewAny,App\Models\DocumentAuditLog');
    });

    // Notification Routes (existing - unchanged)
    Route::post('/notifications/mark-as-read', function (Request $request) {
        if ($request->notification_id) {
            auth()->user()->notifications()
                ->where('id', $request->notification_id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        } else {
            auth()->user()->unreadNotifications->markAsRead();
        }

        return response()->json(['success' => true]);
    })->name('notifications.markAsRead');
});

require __DIR__ . '/auth.php';

// LDAP Admin Routes
Route::middleware(['auth', 'role:Admin'])->prefix('admin/ldap')->name('admin.ldap.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\LdapController::class, 'index'])->name('index');
    Route::get('/test-connection', [App\Http\Controllers\Admin\LdapController::class, 'testConnection'])->name('test-connection');
    Route::post('/test-auth', [App\Http\Controllers\Admin\LdapController::class, 'testAuth'])->name('test-auth');
    Route::post('/user-info', [App\Http\Controllers\Admin\LdapController::class, 'getUserInfo'])->name('user-info');
});

