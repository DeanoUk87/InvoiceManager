<?php

namespace App\Http\Controllers\Traits;
use App\Models\Customers;
use App\Models\Invoices;
use App\Models\Sales;
use App\Models\Settings;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

trait  CustomQuery
{
    /*SALES*/


    /**
     * Create Invoice
     * @param $customer_account
     * @param $invoice_number
     * @param $invoice_date
     * @param $due_date
     * @param $date_created
     * @param $terms
     * @param $sales_id
     */
    public function InsertInvoice($customer_account, $invoice_number, $invoice_date, $due_date, $date_created, $terms, $sales_id, $po_number)
    {
        $db = DB::connection()->getPdo();
        $sql = " REPLACE INTO invoices SET customer_account =:customer_account, invoice_number =:invoice_number, invoice_date =:invoice_date, due_date =:due_date, date_created =:date_created,terms =:terms,sales_id = :sales_id,po_number = :po_number";
        $data = array(':customer_account'=>$customer_account,':invoice_number'=>$invoice_number,':invoice_date'=>$invoice_date,':due_date'=>$due_date,':date_created'=>$date_created,':terms'=>$terms,':sales_id'=>$sales_id,':po_number'=>$po_number);
        $stmt=$db->prepare($sql);
        $stmt->execute($data);
    }

    // INVOICE CUSTOMER NAME
    public function GetCustomerName($customer_account, $invoice_number,$invoice_date)
    {
        return Sales::where('customer_account',$customer_account)
            ->where('invoice_date',$invoice_date)
            ->where('invoice_number',$invoice_number)
            ->orderBy('sales_id','desc')
            ->first();
    }

    /*
     * INVOICE MAKER
    */
    public function GetEmailAccountsMassInvoice($invoice_date=null)
    {
       /* $limitInv = Settings::limit(1)->first()->send_limit;
        if($limitInv){
            $limit = $limitInv;
        }else{
            $limit = 50;
        }*/
        return Customers::join('invoices','invoices.customer_account','=','customers.customer_account')
            ->where('customers.customer_email','!=','')
            ->where('email_status',0)
            ->orderBy('invoice_id')
            //->limit($limit)
            ->get();
    }

    /*INVOICE LIST ITEMS*/
    public function ListItems($customer_account,$invoice_date,$invoice_number)
    {
        return Sales::where('invoice_date',$invoice_date)
            ->where('customer_account',$customer_account)
            ->where('invoice_number',$invoice_number)
            ->orderBy('invoice_date')
            ->get();
    }

    // INVOICE SUM
    public function SumSubTotalMassInvoice($customer_account,$value,$invoice_date=null)
    {
        return Sales::where('customer_account',$customer_account)
            ->where('ms_created',0)
            ->sum($value);
    }

    // INVOICE SUM
    public function SumSubTotal($customer_account,$invoice_date, $invoice_number,$value)
    {
        return Sales::where('customer_account',$customer_account)
            ->where('ms_created',0)
            ->where('customer_account',$customer_account)
            ->where('invoice_date',$invoice_date)
            ->where('invoice_number',$invoice_number)
            ->sum($value);
    }
    // UPDATE
    public function UpdateTerms($terms,$id)
    {
        $db = DB::connection()->getPdo();
        $sql = "UPDATE invoices SET terms =:terms WHERE invoice_id = :id";
        $data = array(':terms'=>$terms,':id'=>$id);
        $stmt=$db->prepare($sql);
        $stmt->execute($data);
    }

    // UPDATE PRINT STATUS
    public function UpdatePrint($customer_account,$invoice_date,$invoice_number)
    {
            DB::table('invoices')
                ->where('printer', 0)
                ->where('customer_account',$customer_account)
                ->where('invoice_date',$invoice_date)
                ->where('invoice_number',$invoice_number)
                ->update(['printer' => 1]);

    }

    /*
     * PROFILE
    */
    // SELECT ONE BY ACCOUNT
    public function SelectOneAccountEmail($id)
    {
        return Customers::where('customer_account',$id)
            ->where('customer_email','!=','')
            ->first();
    }

    // SELECT ONE
    public function SelectInvoice($customer_account,$invoice_date,$invoice_number)
    {
        return Invoices::where('customer_account',$customer_account)
            ->where('invoice_date',$invoice_date)
            ->where('invoice_number',$invoice_number)
            ->limit(1)
            ->first();
    }


    // SELECT ONE BY ACCOUNT
    public function SelectOneAccount($id)
    {
        return Customers::where('customer_account',$id)->first();
    }

    public function AppSettings()
    {
        return Settings::limit(1)->first();
    }

}



