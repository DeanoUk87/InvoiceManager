<?php

namespace App\Mail;

use App\Models\Messagingsettings;
use App\Models\Settings;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Notices extends Mailable
{
    use Queueable, SerializesModels;
    public $title;
    public $message;
    public $filename;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($title,$message,$filename)
    {
        $this->title = $title;
        $this->message = $message;
        $this->filename = $filename;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $setting = Settings::limit(1)->first();
        $title=$this->title;
        $body=$this->message;
        $filename = $this->filename;
        if($filename) {
            return $this->view('mail.Notices', compact('title', 'body'))
                ->subject($title)
                ->from($setting->cemail, $setting->company_name)
                ->attach(public_path('uploads/'.$filename));
        }else{
            return $this->view('mail.Notices', compact('title', 'body'))
                ->subject($title)
                ->from($setting->cemail, $setting->company_name);
        }
    }

    /*SEND MAIL DIRECT FROM FORM WITH ATTACHMENT*/
    /*public function build()
    {
        $setting = Settings::limit(1)->first();
        $title=$this->title;
        $body=$this->message;
        $filename = $this->filename;
        if($filename) {
            return $this->view('mail.Notices', compact('title', 'body'))
                ->subject($title)
                ->from($setting->cemail, $setting->company_name)
                ->attach($filename->getRealPath(),
                    [
                        'as' => $filename->getClientOriginalExtension(),
                        'mime' => $filename->getMimeType()
                    ]);
        }else{
            return $this->view('mail.Notices', compact('title', 'body'))
                ->subject($title)
                ->from($setting->cemail, $setting->company_name);
        }
    }*/
}
