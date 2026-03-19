<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $email;
    protected $email_bcc;
    protected $emailClass;
   /**
    * Create a new job instance.
    *
    * @return void
    */

   public function __construct($email, $emailClass, $email_bcc=null)
   {
       $this->email = $email;
       $this->email_bcc = $email_bcc;
       $this->emailClass= $emailClass;
   }

   /**
    * Execute the job.
    *
    * @return void
    */
   public function handle()
   {
       if($this->email_bcc) {
           Mail::to($this->email)->bcc($this->email_bcc)->send($this->emailClass);
       }else{
           Mail::to($this->email)->send($this->emailClass);
       }
   }
}
