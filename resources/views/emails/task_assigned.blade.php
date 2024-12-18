<p>You have been assigned a new task: {{ $task->name }}</p>
<p>Project: {{ $task->project->name }}</p>
<p>Due date: {{ $task->due_date }}</p>
<p>click the link for more information</p>
<!-- Button to redirect to your website -->
<p>
    <a href="{{ url('http://127.0.0.1:8001/projects/' . $task->project->id) }}">Go to TaskFlow</a>
</p>
