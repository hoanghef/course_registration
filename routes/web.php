<?php

use Illuminate\Support\Facades\Route;

// Serve the frontend login page at root
Route::view('/', 'frontend.index');

// You can add additional simple view routes here, for example:
// Route::view('/student', 'frontend.student-dashboard');
