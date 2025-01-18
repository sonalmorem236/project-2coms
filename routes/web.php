<?php

use App\Http\Controllers\LeadsController;
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
    // return view('welcome');
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->prefix('/leads/')->name('lead.')->controller(LeadsController::class)->group(function(){
    Route::get('', 'index')->name('index');
    Route::get('create', 'create')->name('create');
    Route::post('import', 'store')->name('import');
    Route::post('update', 'update')->name('update');
    Route::post('delete', 'delete')->name('delete');
    Route::post('statusChange', 'statusChange')->name('status.change');
});

require __DIR__.'/auth.php';
