<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('dashboard', [ProjectController::class, 'index'])->name('dashboard');
    Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

    Route::post('/projects/{project}/add-user', [ProjectController::class, 'addUserToProject'])->name('projects.addUser');
    Route::post('/invitations/{invitation}/accept', [ProjectController::class, 'acceptInvitation'])->name('invitations.accept');
    Route::get('/invitations/{invitation}/accept', [ProjectController::class, 'acceptInvitation'])->name('invitations.accept');

    Route::post('/projects/{project}/tasks', [ProjectController::class, 'createTask'])->name('projects.createTask');

    Route::post('/tasks/{taskId}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::get('tasks/{taskId}/download-output', [TaskController::class, 'downloadOutput'])->name('tasks.downloadOutput');

    Route::post('/send-chat-message/{projectId}', [ProjectController::class, 'sendMessage']);
    Route::get('/get-chat-messages/{projectId}', [ProjectController::class, 'getMessages'])->name('get.chat.messages');

    Broadcast::channel('project.{projectId}', function ($user, $projectId) {
        return $user->projects->contains($projectId); // Ensure the user has access to the project
    });

    Route::resource('tasks', TaskController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
