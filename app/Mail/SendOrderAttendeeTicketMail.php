<?php

namespace App\Mail;

use App\Generators\TicketGenerator;
use App\Models\Attendee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class SendOrderAttendeeTicketMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The Attendee instance.
     *
     * @var Attendee
     */
    public $attendee;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Attendee $attendee)
    {
        $this->attendee = $attendee;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::info('Sending ticket to: ' . $this->attendee->email);

        $pdf_file = TicketGenerator::generateFileName($this->attendee->getReferenceAttribute());

        $subject = trans(
            'Controllers.tickets_for_event',
            ['event' => $this->attendee->event->title]
        );

        return $this->subject($subject)
                    ->attach($pdf_file['fullpath'])
                    ->view('Emails.OrderAttendeeTicket');
    }
}
