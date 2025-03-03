<?php

namespace App\Listeners;

use App\Events\PasswordResetRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\View;

class SendPasswordResetEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PasswordResetRequest $event)
    {
        $to = decrypt($event->email);
        $subject = "Reset Your Password";
        $user_id = $event->user_id;
        $email = $event->email;
        $token = $event->token;
        
        // Generate password reset link
        $resetUrl = env('RESET_PAGE_URL');
        $resetLink = "{$resetUrl}?user_id={$user_id}&email={$email}&token={$token}";

        // Render email content from view
        $emailContent = View::make('emails.password_reset', compact('resetLink'))->render();

        // Set email headers
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "From: no-reply@yourdomain.com" . "\r\n";

        // Send email using PHP mail function
        mail($to, $subject, $emailContent, $headers);
    }
}
