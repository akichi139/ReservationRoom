<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $reserve_name;
    public $room_name;
    public $start_time;
    public $stop_time;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($reserve)
    {
        $this->title = $reserve['title'];
        $this->reserve_name = $reserve['name'];
        $this->room_name = $reserve['room_name'];
        $this->start_time = $reserve['start_time'];
        $this->stop_time = $reserve['stop_time'];
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: "$this->title",
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'mail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }

    public function build()
    {
        return $this->from('testSender@example.com')
                    ->view('mail');
    }
}
