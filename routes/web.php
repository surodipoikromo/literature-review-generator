<?php
use App\Http\Controllers\LiteratureController;
use Illuminate\Support\Facades\Route;
Route::get('/',[LiteratureController::class,'index'])->name('literature.index');
Route::post('/search',[LiteratureController::class,'search'])->middleware('throttle:15,1')->name('literature.search');
Route::get('/export/{format}',[LiteratureController::class,'export'])->whereIn('format',['txt','md'])->name('literature.export');
