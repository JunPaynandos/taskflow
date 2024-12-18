@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="my-4">Task Output</h1>
        <h2>Task: {{ $task->name }}</h2>

        @if ($task->output_file_path)
            <h4>Output File:</h4>
            <a href="{{ Storage::url($task->output_file_path) }}" target="_blank" class="btn btn-info">View Output</a>
        @else
            <p>No output file available for this task.</p>
        @endif
    </div>
@endsection
