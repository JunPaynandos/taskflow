@extends('layouts.app')

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4" style="font-size: 2rem;">Create Project</h1>

        <form method="POST" action="{{ route('projects.store') }}">
            @csrf
            <div class="form-group mb-3">
                <label for="name" class="form-label">Project Name</label>
                <input type="text" name="name" id="name" class="form-control" required placeholder="Enter project name">
            </div>
            <div class="form-group mb-3">
                <label for="description" class="form-label">Project Description</label>
                <textarea name="description" id="description" class="form-control" placeholder="Enter project description" rows="4"></textarea>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Create Project</button>
        </form>

        <a href="javascript:history.back()" class="btn btn-secondary mt-4">Go Back</a>
    </div>
@endsection
