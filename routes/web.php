<?php

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

// Redirect 301 - Permanent redirects
Route::redirect('/hoc-vien-cong-nghe-buu-chinh-vien-thong.html', '/hoc-vien-cong-nghe-buu-chinh-vien-thong-ptit.html', 301);
Route::redirect('/hoc-vien-tai-chinh.html', '/hoc-vien-tai-chinh-aof.html', 301);
Route::redirect('/dai-hoc-kinh-te-quoc-dan.html', '/dai-hoc-kinh-te-quoc-dan-neu.html', 301);
Route::redirect('/dai-hoc-thai-nguyen.html', '/dai-hoc-thai-nguyen-tnu.html', 301);
Route::redirect('/dai-hoc-mo-ha-noi.html', '/dai-hoc-mo-ha-noi-hou.html', 301);

// Load modular route files
require __DIR__.'/auth.php';
require __DIR__.'/frontend.php';
require __DIR__.'/ajax.php';
require __DIR__.'/backend.php';
