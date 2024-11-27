<?php

use Illuminate\Support\Facades\Route;

Route::view('/login', 'spa');

// Serve the Vue.js SPA
Route::view('/{any}', 'spa')->where('any', '.*');