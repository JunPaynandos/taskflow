@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Invitation to Join Project: {{ $project->name }}</h1>
        <p>You have been invited to join the project. Do you want to accept the invitation?</p>
        <form action="{{ route('invitations.accept', $invitation->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success">Accept</button>
        </form>
    </div>
@endsection
