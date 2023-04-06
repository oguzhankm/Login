<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\UserController;
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

Route::get('/register', [UserController::class, 'register']);
Route::get('/forgot', [UserController::class, 'forgot']);
Route::get('/reset/{email}/{token}', [UserController::class, 'reset'])->name('reset');

Route::post('/register', [UserController::class, 'saveUser'])->name('auth.register');
Route::post('/login', [UserController::class, 'loginUser'])->name('auth.login');
Route::post('/forgot', [UserController::class, 'forgotPassword'])->name('auth.forgot');

Route::post('/reset-password', [UserController::class, 'resetPassword'])->name('auth.reset');

Route::group(['middleware' => ['LoginCheck']], function (){
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::get('/', [UserController::class, 'index']);
    Route::get('/logout', [UserController::class, 'logout'])->name('auth.logout');
});

Route::middleware(['auth', 'admin'])->name('admin.')->prefix('admin')->group(function (){
    Route::get('/', [\App\Http\Controllers\Admin\AdminController::class, 'index'])->name('index');
    Route::resource('/categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('/menus', \App\Http\Controllers\Admin\MenuController::class);
});
