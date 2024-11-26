<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BotController;

Route::get('/question', [BotController::class, 'getQuestion']);
Route::post('/answer', [BotController::class, 'checkAnswer']);
