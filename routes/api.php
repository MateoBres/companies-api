<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// autenticazione utenti gestita da Sanctum middleware
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/company', [CompanyController::class, 'store'])->name('company.store');
    Route::get('/company/{id}', [CompanyController::class, 'show'])->name('company.show');
    Route::patch('/company/{id}', [CompanyController::class, 'update'])->name('company.update');
    Route::delete('/company/{id}', [CompanyController::class, 'destroy'])->name('company.destroy');
});

// l'utente non loggato puo' vedere solo l'elenco generale
Route::get('/company', [CompanyController::class, 'index'])->name('company.index');

// REGISTRAZIONE
Route::post('/register', RegisterController::class)->name('user.register');

// LOGIN
Route::post('/login', LoginController::class)->name('user.login');