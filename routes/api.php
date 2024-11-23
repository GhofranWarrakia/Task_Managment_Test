<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ReportController;


Route::middleware('auth:api')->group(function () {
    // Routes for Tasks
    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
        Route::post('/', [TaskController::class, 'store'])->name('tasks.store');
        Route::put('/{id}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
        Route::put('/{id}/reassign', [TaskController::class, 'reassign'])->name('tasks.reassign');
        Route::post('/{id}/dependencies', [TaskController::class, 'addDependency'])->name('tasks.addDependency');
        Route::post('/{id}/attachments', [TaskController::class, 'addAttachment'])->name('tasks.addAttachment');
        Route::delete('/{id}', [TaskController::class, 'deleteTask'])->name('tasks.delete');
        Route::put('/{id}/restore', [TaskController::class, 'restore'])->name('tasks.restore');
    });

    // Routes for Reports
    Route::prefix('reports')->group(function () {
        Route::get('/daily-tasks', [ReportController::class, 'dailyTasksReport'])->name('reports.dailyTasks');
        Route::get('/completed-tasks', [ReportController::class, 'completedTasksReport'])->name('reports.completedTasks');
        Route::get('/overdue-tasks', [ReportController::class, 'overdueTasksReport'])->name('reports.overdueTasks');
        Route::get('/user-tasks/{userId}', [ReportController::class, 'userTasksReport'])->name('reports.userTasks');
    });
});
