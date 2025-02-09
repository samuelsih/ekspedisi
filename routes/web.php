<?php

use App\Http\Controllers\SurveyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SurveyController::class, 'index']);
Route::get('/customer', [SurveyController::class, 'searchCustomerID']);
Route::get('/driver', [SurveyController::class, 'searchDriverNIK']);
