<?php

use Illuminate\Support\Facades\Route;
use Leantime\Domain\Envvar\Hxcontrollers\Capture;

Route::get('/envvar/capture', [Capture::class, 'get']);
Route::post('/envvar/capture', [Capture::class, 'store']);
