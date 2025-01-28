<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/create', [MessageController::class, 'showCreateForm']);
Route::post('/create', [MessageController::class, 'createMessage']);

Route::get('/read', [MessageController::class, 'showReadForm']);
Route::post('/read', [MessageController::class, 'readMessage']);
