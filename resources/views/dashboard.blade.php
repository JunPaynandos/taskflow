@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Welcome, {{ auth()->user()->name }}!</h1>
        <p>Your dashboard is here.</p>

        <!-- Button to create a new project -->
        <a href="{{ route('projects.create') }}" class="btn btn-success">Add New Project</a>

        <!-- Display the user's projects -->
        <h2>Your Projects</h2>
        @if ($projects->isEmpty())
            <p>You have not created any projects yet.</p>
        @else
            <ul>
                @foreach ($projects as $project)
                    <li>
                        <!-- Make each project name a clickable link to the show page -->
                        <a href="{{ route('projects.show', $project->id) }}">{{ $project->name }}</a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
