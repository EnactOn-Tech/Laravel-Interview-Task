<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProbabilityController;

Route::resource('/probability',ProbabilityController::class);
Route::post('simulation-process',[ProbabilityController::class,'simulationProcess'])->name('simulation.process');
Route::get('reset-simulation',[ProbabilityController::class,'resetSimulation'])->name('reset.simulation');
