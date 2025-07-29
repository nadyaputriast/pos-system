<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/feedback', [FeedbackController::class, 'showForm']);
Route::post('/feedback', [FeedbackController::class, 'submit']);
Route::get('/invoice/{id}/print', [InvoiceController::class, 'print'])->name('invoice.print');
