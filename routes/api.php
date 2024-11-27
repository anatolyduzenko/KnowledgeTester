<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/questions', [QuizController::class, 'getQuestions']);
    Route::post('/evaluate', [QuizController::class, 'evaluateAnswer']);
    Route::get('/results', [QuizController::class, 'getResults']);
    Route::post('/logout', function (Request $request) {
        // $request->user()->currentAccessToken()->delete();
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    });
});