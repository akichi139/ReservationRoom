<?php

namespace App\Listeners;

use App\Events\PermissionStatusChanged;
use App\Mail\ChangeStatusResponseMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendPermissionStatusChangedEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\PermissionStatusChanged  $event
     * @return void
     */
    public function handle(PermissionStatusChanged $event)
    {
        Mail::to($event->reserve->user->email)->send(new ChangeStatusResponseMail($event->reserve));
    }
}
