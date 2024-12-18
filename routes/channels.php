<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

Broadcast::channel('project.{projectId}', function ($user, $projectId) {
    return $user->projects->contains($projectId); // Ensure the user has access to the project
});
