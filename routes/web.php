<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});



Route::get('/image-upload', [ImageController::class, 'showForm'])->name('image.upload');
Route::post('/convert-image', [ImageController::class, 'convertImage'])->name('image.convert');

Route::get('/form-a', [ImageController::class, 'showFormA'])->name('formA');
Route::post('/form-a', [ImageController::class, 'convertImagesA'])->name('convertImagesA');
Route::get('/form-b', [ImageController::class, 'showFormB'])->name('formB');
Route::post('/form-b', [ImageController::class, 'convertImagesB'])->name('convertImagesB');
Route::get('/form-c', [ImageController::class, 'showFormC'])->name('formC');
