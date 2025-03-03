<?php

namespace App\Listeners;

use App\Events\ContactFormSubmitted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendContactFormNotification
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
    public function handle(ContactFormSubmitted $event): void
    {
        $from_email = 'admin@aiguy.com';
        $to = "dev.devmont@mailinator.com"; // Change this to your recipient email
        $subject = "New Contact Form Submission";
        $headers = "From: " . $from_email . "\r\n" .
                   "Content-Type: text/plain; charset=UTF-8";

        $message = "Name: {$event->name}\n";
        $message .= "Email: {$event->email}\n";
        $message .= "Message:\n{$event->message}\n";

        // Send email using PHP mail() function
        mail($to, $subject, $message, $headers);
    }
}
