<?php

use App\Http\Controllers\DocsController;
use App\Http\Controllers\ShowIconController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::get('/docs/{version?}/{page?}', DocsController::class)->name('docs');
