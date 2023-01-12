<?php

namespace App\Jobs;

use App\Mail\SendMessageToAttendeeMail;
use App\Models\Attendee;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Config;
use Mail;

class SendMessageToAttendeeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $subject;
    public $content;
    public $event;
    public $attendee;
    public $send_copy;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($subject, $content, Event $event, Attendee $attendee, $send_copy)
    {
        $this->subject = $subject;
        $this->content = $content;
        $this->event = $event;
        $this->attendee = $attendee;
        $this->send_copy = $send_copy;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mail = new SendMessageToAttendeeMail(
            $this->subject,
            $this->content,
            $this->event,
            $this->attendee
        );
        Mail::to($this->attendee->email, $this->attendee->full_name)
            ->locale(Config::get('app.locale'))
            ->send($mail);

        if ($this->send_copy == '1') {
            $mail->subject = $mail->subject . trans("Email.organiser_copy");
            Mail::to($this->event->organiser->email, $this->event->organiser->name)
                ->locale(Config::get('app.locale'))
                ->send($mail);
        }
    }
}
