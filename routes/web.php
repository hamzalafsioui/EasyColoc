<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    // ->middleware(['auth', 'verified'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // colocation
    Route::resource('colocations', ColocationController::class);
    Route::post('colocations/{colocation}/cancel', [ColocationController::class, 'cancel'])->name('colocations.cancel');
    Route::get('colocations/{colocation}/balances', [ColocationController::class, 'balances'])->name('colocations.balances');

    // Invitations
    Route::get('colocations/{colocation}/invitations', [InvitationController::class, 'index'])->name('invitations.index');
    Route::post('colocations/{colocation}/invitations', [InvitationController::class, 'create'])->name('invitations.create');
    Route::get('invitations/{token}', [InvitationController::class, 'show'])->name('invitations.show');
    Route::post('invitations/{token}/accept', [InvitationController::class, 'accept'])->name('invitations.accept');
    Route::post('invitations/{token}/refuse', [InvitationController::class, 'refuse'])->name('invitations.refuse');
    Route::delete('invitations/{invitation}', [InvitationController::class, 'destroy'])->name('invitations.destroy');
    Route::resource('colocations.expenses', ExpenseController::class)->only(['index', 'create', 'store', 'update', 'destroy']);
    Route::resource('colocations.categories', CategoryController::class)->only(['store', 'update', 'destroy']);
    Route::post('colocations/{colocation}/payments', [PaymentController::class, 'store'])->name('colocations.payments.store');
    Route::post('colocations/{colocation}/leave', [MembershipController::class, 'leave'])->name('memberships.leave');
    Route::post('colocations/{colocation}/remove-member/{user}', [MembershipController::class, 'remove'])->name('memberships.remove');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('users.index');
    Route::post('/users/{user}/ban', [AdminDashboardController::class, 'ban'])->name('users.ban');
    Route::post('/users/{user}/unban', [AdminDashboardController::class, 'unban'])->name('users.unban');
});

require __DIR__ . '/auth.php';
