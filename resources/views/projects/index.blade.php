@extends('layouts.app')

@if (session('success'))
    <div class="alert alert-dismissible fade show" id="successAlert" style="position: fixed; background: #22bf76; color: white; top: 20px; right: 20px; z-index: 1050; width: auto; max-width: 350px;">
        {{ session('success') }}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let successAlert = document.getElementById('successAlert');
                if (successAlert) {
                    setTimeout(function () {
                        successAlert.classList.remove('show');
                        successAlert.classList.add('fade');
                    }, 5000);
                }
            });
        </script>
    </div>
@endif

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4" style="font-size: 2rem;">Welcome, {{ auth()->user()->name }}!</h1>
        <a href="{{ route('projects.create') }}" class="btn btn-success mb-4">Add New Project</a>

        <h2 class="mb-3 fs-4">Projects</h2>
        @if ($createdProjects->isEmpty() && $memberProjects->isEmpty())
            <p>You have not created any projects or joined any projects yet.</p>
        @else
            <div class="row">
                @foreach ($createdProjects as $project)
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm rounded @if($project->user_id == auth()->id()) border-primary @endif" id="proj-card">
                            <div class="card-body">
                                <h5 class="card-title" style="font-size: 1.25rem; font-weight: 600;">{{ $project->name }}</h5>
                                <p class="card-text" style="font-size: 0.9rem; color: #555;">
                                    {{ $project->description }}
                                </p>
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('projects.show', $project->id) }}" class="btn btn-primary btn-sm" style="background-color: #007bff; border-color: #007bff; font-size: 0.9rem;">
                                    View Project
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

                @foreach ($memberProjects as $project)
                    @if ($project->user_id != auth()->id())
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm rounded" id="proj-card">
                                <div class="card-body">
                                    <h5 class="card-title" style="font-size: 1.25rem; font-weight: 600;">{{ $project->name }}</h5>
                                    <p class="card-text" style="font-size: 0.9rem; color: #555;">
                                        {{ $project->description }}
                                    </p>
                                    <div class="card-footer">
                                        <a href="{{ route('projects.show', $project->id) }}" class="btn btn-primary btn-sm" 
                                        style="background-color: #007bff; border-color: #007bff; font-size: 0.9rem;">
                                        View Project
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <h2 class="mb-3 fs-4 mt-5">Pending Invitations</h2>
        @if ($pendingInvitations->isEmpty())
            <p>You have no pending invitations.</p>
        @else
        <div class="row">
            @foreach ($pendingInvitations as $invitation)
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm rounded" id="proj-card">
                        <div class="card-body">
                            <h5 class="card-title" style="font-size: 1.25rem; font-weight: 600;">{{ $invitation->project->name }}</h5>
                            <p class="card-text" style="font-size: 0.9rem; color: #555;">
                                {{ $invitation->project->description }}
                            </p>
                            <div class="card-footer">
                                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#acceptInvitationModal{{ $invitation->id }}">
                                    Accept Invitation
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal for Accepting Invitation -->
                <div class="modal fade" id="acceptInvitationModal{{ $invitation->id }}" tabindex="-1" aria-labelledby="acceptInvitationModalLabel{{ $invitation->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="acceptInvitationModalLabel{{ $invitation->id }}">Accept Invitation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to accept the invitation to join the project "{{ $invitation->project->name }}"?</p>
                            </div>
                            <div class="modal-footer">
                                <form action="{{ route('invitations.accept', ['invitation' => $invitation->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">Accept</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
@endif

    </div>
@endsection

<style>
    .card:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease-in-out;
    }

    .card-title:hover {
        color: #0056b3;
    }

    .card {
        height: 150px;
        display: flex;
        flex-direction: column;
        position: relative;        
    }

    #proj-card {
        border:  1.5px solid #000;
    }

    .card-body {
        flex-grow: 1;
        overflow-y: auto;
    }

    .card-footer {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 10px 0;
        text-align: left;
    }

    .border-primary {
        border: 1.5px solid #007bff !important;
    }
</style>
