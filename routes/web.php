<?php

use App\Http\Controllers\PetController;

Route::get('/', [PetController::class, 'index']);
Route::get('/pets', [PetController::class, 'index'])->name('pets.index');
Route::get('/pets/create', [PetController::class, 'create'])->name('pets.create');
Route::post('/pets', [PetController::class, 'store'])->name('pets.store');
Route::get('/pets/{id}/edit', [PetController::class, 'edit'])->name('pets.edit');
Route::put('/pets/{id}', [PetController::class, 'update'])->name('pets.update');
Route::delete('/pets/{id}', [PetController::class, 'destroy'])->name('pets.destroy');
