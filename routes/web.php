<?php

use Illuminate\Support\Facades\Route;

// Serve the Vue.js SPA
Route::view('/{any}', 'spa')->where('any', '.*');