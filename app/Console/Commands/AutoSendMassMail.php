<?php

namespace App\Console\Commands;

use App\Jobs\SendMailJob;
use App\Models\Invoices;
use App\Notifications\AppNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Http\Controllers\Traits\CustomQuery;
use App\Http\Controllers\Traits\Uploader;
use App\Mail\MassMail;
use DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class AutoSendMassMail extends Command
{
    use Uploader,CustomQuery;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mass:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mass invoice sending to customer';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
     /*
     * Mass Invoice Email
     *
     * */
        $this->timeExtension();
        $customers = $this->GetEmailAccountsMassInvoice();
        $emailSent = [];
        $batch = DB::table('invoices')->where('email_status',0)->whereNotNull('batch_no')->count();
        if(count($customers) > 0 and $batch>0){
            foreach($customers as $hmail){
                $customer_account=$hmail->customer_account;
                $invoice_number=$hmail->invoice_number;
                $invoice_date = $hmail->invoice_date;
                $customer_email=trim($hmail->customer_email);
                $customer_email_bcc=trim($hmail->customer_email_bcc);
                $email_status = $hmail->email_status;

                if(strpos($customer_email, ',') === false){
                    if(filter_var($customer_email, FILTER_VALIDATE_EMAIL) and $email_status==0){
                        $invoice =Invoices::select('invoice_id')
                            ->where('printer', 1)
                            ->where('customer_account',$customer_account)
                            ->where('invoice_date',$invoice_date)
                            ->where('invoice_number',$invoice_number);
                        if($invoice->count())
                        Cache::lock("invoice-{$invoice->first()->invoice_id}-update")
                            ->get(function () use ($invoice, $customer_account, $invoice_date, $invoice_number, $customer_email, $customer_email_bcc) {
                                if($invoice->update(['printer' => 2, 'email_status'=>1]))
                                dispatch(new SendMailJob($customer_email, new MassMail($customer_account, $invoice_number, $invoice_date, $customer_email,$customer_email_bcc), $customer_email_bcc));
                        });
                    }else{
                        $details = [
                            'subject' => 'Invalid Email Address',
                            'from' => 'no-reply@dh-apps.co.uk',
                            'greeting' => 'Invalid email address',
                            'body' => '<p style="text-align: left">Invalid email address or a duplicate '.$customer_email.'</p>',
                        ];
                        Notification::route('mail', env('MAIL_FROM_ADDRESS'))->notify(new AppNotification($details));
                    }
                }
                else {
                    $custEmails = explode(',',$customer_email);
                    foreach($custEmails as $customerEmail){
                        if(filter_var($customerEmail, FILTER_VALIDATE_EMAIL) and $email_status==0){
                            $invoice = Invoices::select('invoice_id')
                                ->where('printer', 1)
                                ->where('customer_account',$customer_account)
                                ->where('invoice_date',$invoice_date)
                                ->where('invoice_number',$invoice_number);
                            if($invoice->count())
                            Cache::lock("invoice-{$invoice->first()->invoice_id}-update")
                                ->get(function () use ($invoice, $customer_account, $invoice_date, $invoice_number,$customerEmail, $customer_email_bcc) {
                                    if($invoice->update(['printer' => 2, 'email_status'=>1]))
                                    dispatch(new SendMailJob($customerEmail, new MassMail($customer_account, $invoice_number, $invoice_date, $customerEmail,$customer_email_bcc), $customer_email_bcc));
                                });
                        }else{
                            $details = [
                                'subject' => 'Invalid Email Address',
                                'from' => 'no-reply@dh-apps.co.uk',
                                'greeting' => 'Invalid email address',
                                'body' => '<p style="text-align: left">Invalid email address or a duplicate '.$customer_email.'</p>',
                            ];
                            Notification::route('mail', env('MAIL_FROM_ADDRESS'))->notify(new AppNotification($details));
                        }
                    }
                }

                DB::table('sales')->where('ms_created', 0)
                    ->where('customer_account',$customer_account)
                    ->where('invoice_date',$invoice_date)
                    ->where('invoice_number',$invoice_number)
                    ->update(['ms_created' => 1]);
                //$emailSent[]= '<span class="text-success">Mail sent to '.$hmail->customer_email.'</span>';
            }
        }
    }

}
