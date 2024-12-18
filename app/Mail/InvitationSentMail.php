<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Project;
use App\Models\User;
use App\Models\Invitation;

class InvitationSentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $project;
    public $invitation;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Project $project, Invitation $invitation)
    {
        $this->user = $user;
        $this->project = $project;
        $this->invitation = $invitation; 
    }

    public function build()
    {
        return $this->view('emails.invitation')
                    ->with([
                        'userName' => $this->user->name,
                        'projectName' => $this->project->name,
                        'invitationLink' => route('invitations.accept', ['invitation' => $this->invitation->id]),  // Adjust with actual invitation ID
                    ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You are invited to join a project!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invitation',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
