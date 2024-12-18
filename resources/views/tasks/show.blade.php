{{-- resources/views/projects/show.blade.php --}}

@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $project->name }}: Tasks</h1>

        <!-- Button to open the modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTaskModal">
            Create Task
        </button>

        <!-- Task creation modal -->
        <div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createTaskModalLabel">Create Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('tasks.store', $project->id) }}" method="POST">
                            @csrf

                            <!-- Task Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Task Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <!-- Task Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Task Description</label>
                                <textarea class="form-control" id="description" name="description" required></textarea>
                            </div>

                            <!-- Task Status -->
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>

                            <!-- Assigned To -->
                            <div class="mb-3">
                                <label for="assigned_to" class="form-label">Assign To</label>
                                <select class="form-select" id="assigned_to" name="assigned_to" required>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Due Date -->
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" class="form-control" id="due_date" name="due_date" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Create Task</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Task List -->
        <h3>Task List</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($project->tasks as $task)
                    <tr>
                        <td>{{ $task->name }}</td>
                        <td>{{ $task->description }}</td>
                        <td>{{ $task->status }}</td>
                        <td>{{ $task->assignedTo->name }}</td>
                        <td>{{ $task->due_date->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
