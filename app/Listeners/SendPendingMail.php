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
        $data = $event->reserve;

        $mail = new ResponseMail(['title' => $data->title, 'name' => $data->name, 'room_name' => $data->room->room_name, 'start_time' => $data->start_time, 'stop_time' => $data->stop_time]);
        Mail::to('test1@gmail.com')->send($mail);
    }
}