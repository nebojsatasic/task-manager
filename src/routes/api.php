<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
Use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('user', [UserController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('/projects', ProjectController::class);
    Route::post('projects/{project}/members/{user}', [ProjectController::class, 'attachMember'])->name('projects.members.attach');
    Route::delete('projects/{project}/members/{user}', [ProjectController::class, 'detachMember'])->name('projects.members.detach');
    Route::apiResource('/tasks', TaskController::class);
    Route::get('me/tasks', [UserController::class, 'myTasks'])->name('me.tasks');
    Route::patch('tasks/{task}/assign/{user}', [TaskController::class, 'assign'])->name('tasks.members.assign');
    Route::patch('tasks/{task}/unassign', [TaskController::class, 'unassign'])->name('tasks.members.unassign');
    Route::get('me/tasks/assigned', [UserController::class, 'assignedTasks'])->name('me.tasks.assigned');
});

// public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
