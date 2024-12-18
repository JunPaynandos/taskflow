<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Mail\TaskAssignedMail;
use App\Models\Invitation;
use App\Mail\InvitationSentMail;
use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Support\Facades\Log; 

class ProjectController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $createdProjects = $user->projects;

        $memberProjects = $user->projects()
                                ->whereHas('invitations', function ($query) {
                                    $query->where('status', 'accepted');
                                })
                                ->orWhereHas('users', function ($query) use ($user) {
                                    $query->where('users.id', $user->id);
                                })
                                ->get();

        $pendingInvitations = Invitation::where('user_id', $user->id)
                                        ->where('status', 'pending')
                                        ->with('project')
                                        ->get();

        return view('projects.index', compact('createdProjects', 'memberProjects', 'pendingInvitations'));
    }

    public function create()
    {
        $users = User::all();
        return view('projects.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = auth()->user()->projects()->create($request->only('name', 'description'));

        $user = User::find($request->user_id);
        $project->users()->attach($user);
        
        return redirect()->route('dashboard', compact('project'))
                        ->with('success', 'Project created successfully!');
    }

    public function show($projectId)
    {
        $project = Project::findOrFail($projectId);

        $isOwner = auth()->user()->id === $project->user_id;

         // Calculate the overall progress of the project
        $totalTasks = $project->tasks->count(); // Total number of tasks in the project
        $completedTasks = $project->tasks->where('status', 'completed')->count(); // Number of completed tasks
        $progress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0; // Overall progress percentage

        $memberProgress = [];
        foreach ($project->users as $user) {
            $userTasks = $project->tasks->where('assigned_to', $user->id); // Tasks assigned to the user
            $userTotalTasks = $userTasks->count(); // Total tasks for the user
            $userCompletedTasks = $userTasks->where('status', 'completed')->count(); // Completed tasks for the user
            $userProgress = $userTotalTasks > 0 ? ($userCompletedTasks / $userTotalTasks) * 100 : 0; // User progress percentage

            // Store member progress data
            $memberProgress[$user->id] = [
                'name' => $user->name,
                'progress' => $userProgress,
                'tasks' => $userTasks // Include tasks data for display
            ];
        }

        $allUsers = User::all();

        return view('projects.show', compact('project', 'allUsers', 'progress', 'memberProgress', 'isOwner'));
    }

    public function addUserToProject(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);

        $request->validate([
            'user_email' => 'required|email|exists:users,email',
        ]);

        $userEmail = $request->user_email;

        $user = User::where('email', $userEmail)->firstOrFail();

        $existingUser = $project->users()->where('users.id', $user->id)->exists();
        $existingInvitation = Invitation::where('project_id', $projectId)
                                        ->where('user_id', $user->id)
                                        ->where('status', 'pending')
                                        ->exists();

        if ($existingUser) {
            return redirect()->route('projects.show', $projectId)
                            ->with('error', 'This user is already a member!');
        }

        if ($existingInvitation) {
            return redirect()->route('projects.show', $projectId)
                            ->with('error', 'An invitation has already been sent to this user.');
        }

        $invitation = Invitation::create([
            'project_id' => $projectId,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        Mail::to($user->email)->send(new InvitationSentMail($user, $project, $invitation));

        return redirect()->route('projects.show', $projectId)
                        ->with('success', 'Invitation sent to user!');
    }

    public function acceptInvitation($invitationId)
    {
        $invitation = Invitation::findOrFail($invitationId);

        if ($invitation->status !== 'pending') {
            return redirect()->route('projects.show', $invitation->project_id)
                            ->with('error', 'This invitation has already been accepted or declined.');
        }

        $invitation->status = 'accepted';
        $invitation->save();

        $project = $invitation->project;
        $user = $invitation->user;

        $project->users()->attach($user->id);

        return redirect()->route('projects.show', $project->id)
                        ->with('success', 'You have successfully joined the project!');
    }

    Public function createTask(Request $request, $projectId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:not started,in progress,completed',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'required|date',
        ]);

        $project = Project::findOrFail($projectId);

        $task = new Task();
        $task->name = $request->name;
        $task->description = $request->description;
        $task->status = $request->status;
        $task->assigned_to = $request->assigned_to;
        $task->due_date = $request->due_date;
        $task->project_id = $projectId;
        $task->save();

        $this->sendEmailNotification($task);

        return redirect()->route('projects.show', $projectId)
                        ->with('success', 'Task created successfully!');
    }

    protected function sendEmailNotification(Task $task)
    {
        $user = $task->assignedUser;
        Mail::to($user->email)->send(new TaskAssignedMail($task));
    }

    public function sendMessage(Request $request, $projectId)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        try {
            // Log incoming message request
            Log::info('Received message request', [
                'user_id' => auth()->id(),
                'project_id' => $projectId,
                'message' => $request->message,
            ]);

            // Save the message to the database
            $message = Message::create([
                'project_id' => $projectId,
                'user_id' => auth()->id(),
                'message' => $request->message,
            ]);

            // Log message saved successfully
            Log::info('Message saved successfully', [
                'message_id' => $message->id,
                'user_id' => auth()->id(),
                'project_id' => $projectId,
            ]);

            // Prepare the data for broadcasting
            $messageData = [
                'message' => $message->message,
                'user_name' => $message->user->name, // Get the user name from the relationship
                'created_at' => $message->created_at->format('M d, Y H:i'),
            ];

            // Log the data that will be broadcasted
            Log::info('Broadcasting message with data:', $messageData);

            // Broadcast the message to the specific project channel
            broadcast(new MessageSent($message, $projectId));

            // Log message broadcasted successfully
            Log::info('Message broadcasted successfully', [
                'message_id' => $message->id,
                'user_id' => auth()->id(),
                'project_id' => $projectId,
            ]);

            return response()->json(['status' => 'Message sent', 'data' => $messageData]); // Send response with message data
        } catch (\Exception $e) {
            \Log::error('Error sending message: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while sending the message'], 500);
        }
    }

    public function getMessages($projectId)
    {
        // Fetch the messages for the given project ID
        $messages = Message::where('project_id', $projectId)
            ->with('user') // Ensure the user relationship is loaded to get the sender's name
            ->orderBy('created_at', 'asc') // Order messages by creation time (ascending)
            ->get();

        // Format the messages data to return
        $formattedMessages = $messages->map(function ($message) {
            return [
                'message' => $message->message,
                'user_name' => $message->user->name, // Assuming your Message model has a user relationship
                'created_at' => $message->created_at->format('M d, Y H:i'), // Format the timestamp
            ];
        });

        // Return the formatted messages as JSON
        return response()->json($formattedMessages);
    }  
}
