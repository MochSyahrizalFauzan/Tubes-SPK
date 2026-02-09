<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MachineTypeController;
use App\Http\Controllers\MachineSizeController;
use App\Http\Controllers\ProcessingRuleController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SlotController;



Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
  Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
  Route::resource('machine-types', MachineTypeController::class);
  Route::resource('machine-sizes', MachineSizeController::class);
  Route::resource('processing-rules', ProcessingRuleController::class);
  Route::resource('orders', OrderController::class);

  Route::get('scheduling/run', [ScheduleController::class, 'runForm'])->name('scheduling.runForm');
  Route::post('scheduling/run', [ScheduleController::class, 'run'])->name('scheduling.run');
  Route::get('scheduling/runs', [ScheduleController::class, 'runs'])->name('scheduling.runs');
  Route::get('scheduling/runs/{runId}', [ScheduleController::class, 'results'])->name('scheduling.results');

  Route::get('/slots', [SlotController::class, 'index'])->name('slots.index');
  Route::post('/slots/{slotId}/toggle', [SlotController::class, 'toggle'])->name('slots.toggle');
  Route::post('/slots/seed-default', [SlotController::class, 'seedDefault'])->name('slots.seed');




  Route::resource('customers', CustomerController::class);
});

require __DIR__.'/auth.php';
