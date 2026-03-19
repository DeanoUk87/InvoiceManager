<?php

namespace App\Http\Controllers\Traits;
use App\Models\Archive\Customers;
use App\Models\Archive\Invoices;
use App\Models\Archive\Sales;
use App\Models\Settings;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

trait  CustomQuery2
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
    public function InsertInvoice($customer_account, $invoice_number, $invoice_date, $due_date, $date_created, $terms, $sales_id)
    {
        $db = DB::connection()->getPdo();
        $sql = " REPLACE INTO invoices SET customer_account =:customer_account, invoice_number =:invoice_number, invoice_date =:invoice_date, due_date =:due_date, date_created =:date_created,terms =:terms,sales_id = :sales_id ";
        $data = array(':customer_account'=>$customer_account,':invoice_number'=>$invoice_number,':invoice_date'=>$invoice_date,':due_date'=>$due_date,':date_created'=>$date_created,':terms'=>$terms,':sales_id'=>$sales_id);
        $stmt=$db->prepare($sql);
        $stmt->execute($data);
    }

    // INVOICE CUSTOMER NAME
    public function GetCustomerName($customer_account, $invoice_number,$invoice_date)
    {
        return Sales::where('customer_account',$customer_account)
            ->where('invoice_date',$invoice_date)
            ->where('invoice_number',$invoice_number)
            ->limit(1)
            ->first();
    }



    /*
     * INVOICE MAKER
    */
    public function GetEmailAccountsMassInvoice($invoice_date=null)
    {
        return Customers::LeftJoin('sales','sales.customer_account','=','customers_profile.customer_account')
            ->where('customers_profile.customer_email','!=','')
            ->where('sales.ms_created',0)
            ->groupBy(['customers_profile.customer_account','sales.invoice_number'])
            ->get();

    }

    /*INVOICE LIST ITEMS*/
    public function ListItems($customer_account,$invoice_date,$invoice_number)
    {
        return Sales::where('invoice_date',$invoice_date)
            ->where('customer_account',$customer_account)
            ->where('invoice_number',$invoice_number)
            ->orderBy('customer_account')
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
        return Customers::where('customer_account', $id)
            ->where('customer_email', '!=', '')
            ->first();
    }

    // SELECT ONE
    public function SelectInvoice($customer_account,$invoice_date,$invoice_number)
    {
        return Invoices::where('customer_account', $customer_account)
            ->where('invoice_date', $invoice_date)
            ->where('invoice_number', $invoice_number)
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



