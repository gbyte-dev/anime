<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnimeController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/index', function () {
    return view('index');
});
Route::get('/save-anime', [AnimeController::class, 'saveTopAnime']);