<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Completed</title>
</head>
<body>
    <h1>Task Completed</h1>
    <p>The task titled "<strong>{{ $task->name }}</strong>" has been marked as completed.</p>

    <p>Details:</p>
    <ul>
        <li>Status: {{ $task->status }}</li>
        <li>Assigned to: {{ $task->assignedUser ? $task->assignedUser->name : 'Not Assigned' }}</li>
        <li>Due Date: {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : 'N/A' }}</li>
    </ul>

    <p>You can view the project associated with this task by clicking the link below:</p>

    <!-- Link to the project page -->
    <p><a href="{{ url('/projects/' . $task->project->id) }}">Go to the Project</a></p>

    <p>Thank you for managing the project!</p>
</body>
</html>
