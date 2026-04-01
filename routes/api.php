<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Artisan;

Route::get('/tasks/report',[TaskController::class, 'report']);
Route::get('/tasks', [TaskController::class, 'index']);
Route::post('/tasks', [TaskController::class, 'store']);

Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus']);
Route::delete('/tasks/{id}',[TaskController::class, 'destroy']);

Route::get('/setup-database', function () {
    Artisan::call('migrate', ['--force' => true]);
    return response()->json(['message' => 'Database tables created successfully!']);
});