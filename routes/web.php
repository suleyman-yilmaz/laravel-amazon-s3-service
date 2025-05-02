<?php

use App\Http\Controllers\AmazonS3Controller;
use Illuminate\Support\Facades\Route;

Route::get('/', [AmazonS3Controller::class, 'index']);
Route::post('upload', [AmazonS3Controller::class, 'upload'])->name('s3.upload');