<?php

namespace App\Listeners;

use App\Events\PendingMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\ResponseMail;
use Illuminate\Support\Facades\Mail;

class SendPendingMail
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
     * @param  \App\Events\PendingMail  $event
     * @return void
     */
    public function handle(PendingMail $event)
    {
        Mail::to('admin@gmail.com')->send(new ResponseMail($event->reserve));
    }
}