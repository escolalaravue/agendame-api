<?php

namespace App\Listeners;

use App\Mail\InvitationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendInvitationEmail implements ShouldQueue
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
    public function handle(object $event): void
    {
        $teamInvitation = $event->teamInvitation;
        Mail::to($teamInvitation->email)
            ->send(new InvitationMail($teamInvitation));
    }
}
