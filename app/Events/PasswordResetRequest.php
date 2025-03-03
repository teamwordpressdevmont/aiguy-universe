<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PasswordResetRequest
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $email;
    public $token;

    public function __construct($user_id, $email, $token)
    {
        $this->user_id = encrypt($user_id); // Hash the user_id
        $this->email = encrypt($email);     // Hash the email
        $this->token = encrypt($token);    // Hash the token
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
