<?php

use Illuminate\Support\Facades\Route;

// SPA - todas as rotas retornam a view principal
// O Vue Router vai cuidar das rotas no frontend
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
