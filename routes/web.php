<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('territories', App\Http\Controllers\TerritoryController::class);
    Route::post('territories/{territory}/persons/reorder', [App\Http\Controllers\TerritoryController::class, 'reorderPersons'])->name('territories.persons.reorder');
    Route::resource('persons', App\Http\Controllers\PersonController::class);
    Route::get('/admin/settings', [App\Http\Controllers\AdminSettingsController::class, 'index'])->name('admin.settings');
    Route::post('/admin/settings', [App\Http\Controllers\AdminSettingsController::class, 'update'])->name('admin.settings.update');

    // Territories Import/Export
    Route::get('/admin/territories/export-template', [App\Http\Controllers\TerritoryImportExportController::class, 'exportTemplate'])->name('admin.territories.export-template');
    Route::get('/admin/territories/export-backup', [App\Http\Controllers\TerritoryImportExportController::class, 'exportBackup'])->name('admin.territories.export-backup');
    Route::post('/admin/territories/import', [App\Http\Controllers\TerritoryImportExportController::class, 'import'])->name('admin.territories.import');

    Route::get('/admin/territories', [App\Http\Controllers\AdminTerritoryController::class, 'index'])->name('admin.territories.index');

    Route::resource('cities', App\Http\Controllers\CityController::class);
    Route::resource('assignments', App\Http\Controllers\TerritoryAssignmentController::class);
    Route::post('users/{user}/restore', [App\Http\Controllers\AdminUserController::class, 'restore'])->name('users.restore')->withTrashed();
    Route::resource('users', App\Http\Controllers\AdminUserController::class);
    Route::resource('admin/personal-territories', App\Http\Controllers\AdminPersonalTerritoryController::class, [
        'as' => 'admin'
    ])->except(['show', 'edit']);

    Route::get('/export/assignments', [App\Http\Controllers\PdfController::class, 'exportAssignments'])->name('export.assignments');

    // Approvals & Inactive Routes (Admin Only)
    Route::group(['middleware' => ['role:admin']], function () {
        Route::get('/approvals', [App\Http\Controllers\ApprovalController::class, 'index'])->name('approvals.index');
        Route::post('/approvals/{person}/approve', [App\Http\Controllers\ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/{person}/reject', [App\Http\Controllers\ApprovalController::class, 'reject'])->name('approvals.reject');

        // Territory Requests Approvals
        Route::post('/approvals/territory-requests/{territoryRequest}/approve', [App\Http\Controllers\ApprovalController::class, 'approveTerritoryRequest'])->name('approvals.territory-request.approve');
        Route::post('/approvals/territory-requests/{territoryRequest}/reject', [App\Http\Controllers\ApprovalController::class, 'rejectTerritoryRequest'])->name('approvals.territory-request.reject');

        // Inactive Persons
        Route::get('/admin/inactive-persons', [App\Http\Controllers\InactivePersonController::class, 'index'])->name('admin.persons.inactive');
        Route::post('/admin/inactive-persons/{person}/activate', [App\Http\Controllers\InactivePersonController::class, 'activate'])->name('admin.persons.activate');

        // User Approvals
        Route::post('/approvals/users/{user}/approve', [App\Http\Controllers\ApprovalController::class, 'approveUser'])->name('approvals.user.approve');
        Route::post('/approvals/users/{user}/reject', [App\Http\Controllers\ApprovalController::class, 'rejectUser'])->name('approvals.user.reject');
    });

    // Territory Requests for users
    Route::resource('territory-requests', App\Http\Controllers\TerritoryRequestController::class)->only(['create', 'store']);
});

require __DIR__ . '/auth.php';
