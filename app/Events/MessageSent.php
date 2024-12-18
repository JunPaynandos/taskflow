<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; 
use App\Models\Message;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $projectId;

    public function __construct(Message $message, $projectId)
    {
        $this->message = $message;
        $this->projectId = $projectId;
    }

    public function broadcastOn()
    {
        return new Channel('project.' . $this->projectId);
    }

    public function broadcastWith()
{
    // Log::info('Broadcasting message with data', [
    //     'message' => $this->message->message,
    //     'user_name' => $this->message->user->name,
    //     'created_at' => $this->message->created_at->format('M d, Y H:i'),
    // ]);

    return [
        'message' => $this->message->message,
        'user_name' => $this->message->user->name,
        'created_at' => $this->message->created_at->format('M d, Y H:i'),
    ];
}

public function broadcastAs()
{
    return 'MessageSent';
}

}
