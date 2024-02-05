<?php
/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
// Generate symbolic link
Route::get('/symlink', function () {return view('symlink');})->name('generate_symlink');

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
