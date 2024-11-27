<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/questions', [QuizController::class, 'getQuestions']);
    Route::post('/evaluate', [QuizController::class, 'evaluateAnswer']);
    Route::get('/results', [QuizController::class, 'getResults']);
});