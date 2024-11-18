<?php

use App\Http\Controllers\FormController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    Route::prefix('forms')->group(function () {
        Route::get('/', [FormController::class, 'index'])->name('forms');

        Route::get('/create', [FormController::class, 'create'])->name('forms.create');
        Route::get('/{id}', [FormController::class, 'show'])->name('forms.show');
        Route::get('/edit/{id}', [FormController::class, 'edit'])->name('forms.edit');

        Route::get('/preview/{unique_url}', [FormController::class, 'preview'])->name('forms.preview');
        Route::post('/preview/{unique_url}', [FormController::class, 'previewStore'])->name('forms.preview.store');

        Route::post('/store', [FormController::class, 'store'])->name('forms.store');
        Route::put('/{id}', [FormController::class, 'update'])->name('forms.update');

        Route::prefix('question')->group(function () {
            Route::get('/create/{form_id}', [QuestionController::class, 'create'])->name('questions.create');
            Route::post('/store/{form_id}', [QuestionController::class, 'store'])->name('questions.store');
            Route::get('/{id}', [QuestionController::class, 'edit'])->name('questions.edit');
            Route::put('/{id}', [QuestionController::class, 'update'])->name('questions.update');
            Route::delete('/{id}', [QuestionController::class, 'delete'])->name('questions.delete');
        });
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users');
        // Route::get('/{id}', [UserController::class, 'show'])->name('users.show');

        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/store', [UserController::class, 'store'])->name('users.store');

        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('users.update');

        Route::delete('/{id}', [UserController::class, 'delete'])->name('users.delete');
    });

    Route::prefix('response')->group(function () {
        Route::get('/', [ResponseController::class, 'index'])->name('response');
        Route::get('/{id}', [ResponseController::class, 'show'])->name('response.show');
        Route::get('/detail/{id}', [ResponseController::class, 'detail'])->name('response.detail');
        Route::get('/export-excel/{id}', [ResponseController::class, 'exportExcel'])->name('response.export-excel');
    });
});

Route::get('/d/{unique_url}', [ResponseController::class, 'showForUser'])->name('forms.user');
Route::post('/d/{unique_url}', [ResponseController::class, 'storeForUser'])->name('forms.user.store');
Route::get('/s', [ResponseController::class, 'success'])->name('forms.user.success');

require __DIR__ . '/auth.php';
