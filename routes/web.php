<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // colocation
    Route::resource('colocations', ColocationController::class);
    Route::post('colocations/{colocation}/cancel', [ColocationController::class, 'cancel'])->name('colocations.cancel');
    Route::post('colocations/{colocation}/invitations', [InvitationController::class, 'create'])->name('invitations.create');
    Route::post('invitations/{token}/accept', [InvitationController::class, 'accept'])->name('invitations.accept');
    Route::post('invitations/{token}/refuse', [InvitationController::class, 'refuse'])->name('invitations.refuse');
    Route::resource('colocations.expenses', ExpenseController::class)->only(['store', 'update', 'destroy']);
    Route::resource('colocations.categories', CategoryController::class)->only(['store', 'update', 'destroy']);
    Route::post('colocations/{colocation}/payments', [PaymentController::class, 'store'])->name('colocations.payments.store');
    Route::post('colocations/{colocation}/leave', [MembershipController::class, 'leave'])->name('memberships.leave');
    Route::post('colocations/{colocation}/remove-member/{user}', [MembershipController::class, 'remove'])->name('memberships.remove');
});

require __DIR__ . '/auth.php';
