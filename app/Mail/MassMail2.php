<?php

namespace App\Mail;

use App\Http\Controllers\Traits\CustomQuery2;
use App\Models\Archive\Customers;
use PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MassMail2 extends Mailable
{
    use Queueable, SerializesModels, CustomQuery2;
    public $customer_account;
    public $invoice_number;
    public $invoice_date;
    public $customer_email;
    public $customer_email_bcc;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($customer_account,$invoice_number,$invoice_date,$customer_email,$customer_email_bcc)
    {
        $this->customer_account = $customer_account;
        $this->invoice_number = $invoice_number;
        $this->invoice_date = $invoice_date;
        $this->customer_email = $customer_email;
        $this->customer_email_bcc = $customer_email_bcc;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        ini_set("pcre.backtrack_limit", "5000000");

        $setup = $this->AppSettings();
        $customers = $this->GetEmailAccountsMassInvoice();

        $customer_account=$this->customer_account;
        $invoice_number=$this->invoice_number;
        $invoice_date = $this->invoice_date;
        $customer_email=trim($this->customer_email);
        $customer_email_bcc=trim($this->customer_email_bcc);

        $prof = $this->GetCustomerName($customer_account, $invoice_number,$invoice_date);
        $items = $this->ListItems($customer_account,$invoice_date,$invoice_number);
        $subtotal = $this->SumSubTotalMassInvoice($customer_account,'sub_total');
        $vat = $this->SumSubTotalMassInvoice($customer_account,'vat_percent');
        $invo = $this->SelectInvoice($customer_account, $invoice_number,$invoice_date);
        $customer = $this->SelectOneAccount($customer_account);
        $owner = Customers::where('customer_account',$customer_account)->first();
        //pdf
        $pdf = PDF::loadView('archive.invoices.print-invoice', compact('setup', 'customers', 'prof', 'items', 'subtotal', 'vat', 'customer_account', 'invoice_number', 'invoice_date', 'invo', 'customer','owner'));
        //mail
        $find  = array("{invoice_number}");
        $replace = array($invoice_number);

        $title = $setup->message_title;
        $body=(str_replace($find,$replace,$setup->default_message2));

        if($customer_email_bcc) {
            return $this->view('mail.mailinvoice', compact('title', 'body'))
                ->subject($setup->message_title)
                ->to($customer_email, $customer_account)
                ->bcc($customer_email_bcc, $customer_account)
                ->from($setup->cemail, $setup->company_name)
                ->attachData($pdf->output(), 'invoices_' . $invoice_number . '.pdf');
        }else{
            return $this->view('mail.mailinvoice', compact('title', 'body'))
                ->subject($setup->message_title)
                ->to($customer_email, $customer_account)
                ->from($setup->cemail, $setup->company_name)
                ->attachData($pdf->output(), 'invoices_' . $invoice_number . '.pdf');
        }
    }
}
