<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Printers;
use App\Livewire\Home;

Route::get('/', Home::class);
Route::get('/printers', Printers::class);