<?php

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

Route::get('/', 'PageController@home')->name('home');
Route::post('/create', 'PageController@createBuild')->name('build.create');

Route::post('/login', 'PageController@login')->name('login');
Route::get('/logout', function () {
    session()->remove('xenforo');
    return redirect()->route('home');
})->name('logout');
Route::post('/{hash}', 'PageController@updateBuild')->name('build.update');
Route::get('/{hash}', 'PageController@viewBuild')->name('build.view');
Route::delete('/{hash}', 'PageController@deleteBuild')->name('build.delete');