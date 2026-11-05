<?php

use Illuminate\Support\Facades\Route;

// Public company introduction page. No controller/database changes needed.
Route::view('/about', 'about')->name('hexagon.about');
