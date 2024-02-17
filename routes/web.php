<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

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


    Route::get('/applicant', [ApplicantController::class, 'index'])->name('applicants.index');
Route::get('/create', [ApplicantController::class, 'create'])->name('applicants.create');
Route::post('/store', [ApplicantController::class, 'store'])->name('applicants.store');
Route::get('/show/{applicant}', [ApplicantController::class, 'show'])->name('applicants.show');
Route::get('applicants/{id}/edit', [ApplicantController::class, 'edit'])->name('applicants.edit');
Route::put('applicants/update/{applicant}', [ApplicantController::class, 'update'])->name('applicants.update');
Route::delete('/delete/{applicant}', [ApplicantController::class, 'destroy'])->name('applicants.destroy');
});

require __DIR__.'/auth.php';
