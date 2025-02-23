<?php

use App\Http\Controllers\AntiSurveyController;
use App\Http\Controllers\SurveyController;
use Illuminate\Support\Facades\Route;

// Route::get('/', [SurveyController::class, 'index']);
Route::get('/', [SurveyController::class, 'indexWithoutChannel']);
Route::get('/customer', [SurveyController::class, 'searchCustomerID']);
Route::get('/driver', [SurveyController::class, 'searchDriverNIK']);
// Route::post('/survey', [SurveyController::class, 'store']);
Route::post('/survey', [SurveyController::class, 'storeWithoutChannel']);

Route::get('/decline-survey', [AntiSurveyController::class, 'index']);
Route::post('/decline-survey', [AntiSurveyController::class, 'store']);

Route::get('/login', function () {
    return redirect('/admin');
})->name('login');
