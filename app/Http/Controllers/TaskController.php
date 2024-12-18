<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Mail\TaskCompletedMail;

class TaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        Log::info('Project ID:', [
            'request_project_id' => $request->project_id,
            'project_id_from_route' => $project->id
        ]);
    
        Log::info('Project Model:', ['project' => $project]);
    
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:not started,in progress,completed',
            'due_date' => 'nullable|date',
        ]);
    
        Log::info('Project ID:', ['project_id' => $project->id]);
    
        $task = new Task();
        $task->name = $request->name;
        $task->description = $request->description;
        $task->assigned_to = $request->assigned_to;
        $task->status = $request->status;
        $task->due_date = $request->due_date;
        $task->project_id = $project->id;
        $task->save();
    
        $this->sendEmailNotification($task);
        // $this->sendSlackNotification($task);
    
        return redirect()->route('projects.show', $project->id)
                        ->with('success', 'Task created successfully!');
    }
    

    protected function sendEmailNotification(Task $task)
    {
        try {
            $user = $task->assignedUser;
    
            if ($user) {
                Mail::to($user->email)->send(new TaskAssignedMail($task));
            } else {
                Log::warning("No user assigned to task: {$task->id}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send email for task {$task->id}: " . $e->getMessage());
        }
    }
    public function updateStatus(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);

        // Validate the input
        $request->validate([
            'status' => 'required|in:not started,in progress,completed',
            // Allow file uploads only when the status is not 'not started'
            'output_file' => $request->status !== 'not started' ? 'required|file|mimes:pdf,jpeg,png,jpg,docx|max:10240' : 'nullable|file|mimes:pdf,jpeg,png,jpg,docx|max:10240',
        ]);

        // Update task status
        $task->status = $request->status;

        if ($request->hasFile('output_file')) {
            $file = $request->file('output_file');
            $originalName = $file->getClientOriginalName();  // Get the original file name
            $path = $file->storeAs('task_outputs', $originalName, 'public');  // Save the file with its original name
            $task->output_file_path = $path;
        }

        // Save the task with updated status and output file
        $task->save();

        // Send the email to the project owner if task is completed and output is uploaded
        if ($task->status == 'completed' && $task->output_file_path) {
            // Get the project owner using the user_id in the projects table
            $projectOwner = $task->project->user;  // Assuming 'user' is the relationship name for project owner

            if ($projectOwner) {
                // Send email to the project owner
                try {
                    // Sending email to project owner
                    Mail::to($projectOwner->email)->send(new TaskCompletedMail($task));
                } catch (\Exception $e) {
                    Log::error("Failed to send email for task {$task->id}: " . $e->getMessage());
                }
            } else {
                Log::warning("Task {$task->id} completed, but no project owner found.");
            }
        }
        return redirect()->back()->with('success', 'Task status updated.');
    }

    protected function sendTaskCompletedEmailToOwner(Task $task)
    {
        try {
            $projectOwner = $task->project->users->firstWhere('id', true);

            if ($projectOwner) {
                // Send the email to the project owner
                Mail::to($projectOwner->email)->send(new TaskCompletedMail($task));
            } else {
                Log::warning("No owner found for project: {$task->project->id}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send email for task {$task->id}: " . $e->getMessage());
        }
    }

    public function downloadOutput($taskId)
    {
        $task = Task::findOrFail($taskId);

        if (!$task->output_file_path) {
            return redirect()->route('projects.show', $task->project_id)->with('error', 'No output file found.');
        }

        $filePath = storage_path('app/public/' . $task->output_file_path);

        if (!file_exists($filePath)) {
            return redirect()->route('projects.show', $task->project_id)->with('error', 'File not found.');
        }

        return response()->download($filePath);
    }
}
