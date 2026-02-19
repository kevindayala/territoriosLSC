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
    Route::resource('persons', App\Http\Controllers\PersonController::class);
    Route::get('/admin/settings', [App\Http\Controllers\AdminSettingsController::class, 'index'])->name('admin.settings');
    Route::get('/admin/territories', [App\Http\Controllers\AdminTerritoryController::class, 'index'])->name('admin.territories.index');

    Route::resource('cities', App\Http\Controllers\CityController::class);
    Route::resource('assignments', App\Http\Controllers\TerritoryAssignmentController::class);
    Route::resource('users', App\Http\Controllers\AdminUserController::class);

    Route::get('/export/assignments/{year}', [App\Http\Controllers\PdfController::class, 'exportAssignments'])->name('export.assignments');

    // Approvals & Inactive Routes (Admin Only)
    Route::group(['middleware' => ['role:admin']], function () {
        Route::get('/approvals', [App\Http\Controllers\ApprovalController::class, 'index'])->name('approvals.index');
        Route::post('/approvals/{person}/approve', [App\Http\Controllers\ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/{person}/reject', [App\Http\Controllers\ApprovalController::class, 'reject'])->name('approvals.reject');

        // Inactive Persons
        Route::get('/admin/inactive-persons', [App\Http\Controllers\InactivePersonController::class, 'index'])->name('admin.persons.inactive');
        Route::post('/admin/inactive-persons/{person}/activate', [App\Http\Controllers\InactivePersonController::class, 'activate'])->name('admin.persons.activate');
    });
});

require __DIR__ . '/auth.php';
