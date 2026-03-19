<?php

namespace App\Mail;

use App\Models\Messagingsettings;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyMailSuccess extends Mailable
{
    use Queueable, SerializesModels;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $message=Messagingsettings::where('message_type','mail-signup-welcome')->limit(1)->first();

        $find  = array("{user}","{title}","{login_link}");
        $replace = array($this->user['name'],env('APP_NAME'),route('login'));

        $title=$message->title;
        $body=(str_replace($find,$replace,$message->body));

        return $this->view('mail.verifySuccess',compact('title','body'))->subject(env('APP_NAME').' - Welcome on board') ;
    }
}
