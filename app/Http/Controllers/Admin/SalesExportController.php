<?php
/*
* =======================================================================
* FILE NAME:        InvoicesController.php
* DATE CREATED:  	17-01-2019
* FOR TABLE:  		invoices
* AUTHOR:			DH Fusion Ltd.
* CONTACT:			http://dhfusion.co.uk <info@dhfusion.co.uk>
* =======================================================================
*/
namespace App\Http\Controllers\Admin;
use App\Exports\InvoiceCustomExports;
use App\Http\Controllers\Traits\CustomQuery;
use App\Http\Controllers\Traits\Uploader;

use App\Mail\MassMail;
use App\Models\Customers;
use App\Models\Invoices;

use App\Models\Sales;
use App\Models\System\Upload;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Session;
use Validator;
use Auth;
use Yajra\Datatables\Datatables;
use PDF;
use DB;
use Excel;
use Image;

class SalesExportController extends Controller
{
    use Uploader,CustomQuery;

    /**
     * InvoicesController constructor.
     */
    public function __construct() {
        $this->middleware(['auth', 'verifier']);
    }
    /**
     * This method display invoices view for datatable
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function queries2($fromdate=null,$todate=null,$customer=null,$invoice=null,$sage=null)
    {
        if($sage){
            if ($fromdate and $customer) {
                return Sales::select(
                    'invoice_number', 'customer_account2',
                    'invoice_date', 'job_number', 'invoice_total', 'vat_amount',
                    'percentage_fuel_surcharge','percentage_resourcing_surcharge',
                    DB::raw('ROUND((invoice_total-vat_amount),2) AS net_total')
                )
                    //->where('customer_account',$customer)
                    ->where('customer_account2','!=','')
                    ->whereBetween('invoice_date', [$fromdate, $todate])
                    ->groupBy('invoice_number')
                    ->orderBy('invoice_number','desc');
            }
            elseif ($fromdate and !$customer) {
                return Sales::select(
                    'invoice_number', 'customer_account2',
                    'invoice_date', 'job_number', 'invoice_total', 'vat_amount',
                    'percentage_fuel_surcharge','percentage_resourcing_surcharge',
                    DB::raw('ROUND((invoice_total-vat_amount),2) AS net_total')
                )
                    ->where('customer_account2','!=','')
                    ->whereBetween('invoice_date', [$fromdate, $todate])
                    ->groupBy('invoice_number')
                    ->orderBy('invoice_number','desc');
            }
            else {
                return Sales::select(
                    'invoice_number', 'customer_account2',
                    'invoice_date', 'job_number', 'invoice_total', 'vat_amount',
                    'percentage_fuel_surcharge','percentage_resourcing_surcharge',
                    DB::raw('ROUND((invoice_total-vat_amount),2) AS net_total')
                )
                    ->where('customer_account2','!=','')
                    ->groupBy('invoice_number')
                    ->orderBy('invoice_number','desc');
            }
        }
        //end sage
        else {
            if ($fromdate and $customer) {
                return Sales::whereBetween('invoice_date', [$fromdate, $todate])
                    //->where('customer_account',$customer)
                    ->groupBy('customer_account');
            }
            if (!$fromdate and $customer) {
                return Sales::groupBy('customer_account');
            }
            elseif ($fromdate and !$customer) {
                return Sales::whereBetween('invoice_date', [$fromdate, $todate]);
            }
            else {
                return Sales::query();
            }
        }
    }


    public function queries($fromdate=null,$todate=null,$customer=null,$invoice=null,$sage=null)
    {
        if($sage){
            if ($fromdate) {
                return Sales::select(
                    'invoice_number', 'customer_account2',
                    'invoice_date', 'job_number', 'invoice_total', 'vat_amount',
                    'percentage_fuel_surcharge','percentage_resourcing_surcharge',
                    DB::raw('ROUND((invoice_total-vat_amount),2) AS net_total')
                )
                    ->where('customer_account2','!=','')
                    ->whereBetween('invoice_date', [$fromdate, $todate])
                    ->groupBy('invoice_number')
                    ->orderBy('customer_account','asc')
                    ->get();
            }
            else {
                return Sales::select(
                    'invoice_number', 'customer_account2',
                    'invoice_date', 'job_number', 'invoice_total', 'vat_amount',
                    'percentage_fuel_surcharge','percentage_resourcing_surcharge',
                    DB::raw('ROUND((invoice_total-vat_amount),2) AS net_total')
                )
                    ->where('customer_account2','!=','')
                    ->groupBy('invoice_number')
                    ->orderBy('customer_account','asc')
                    ->get();
            }
        }
        //end sage
        else {
            if ($fromdate) {
                return Sales::whereBetween('invoice_date', [$fromdate, $todate])->get();
            }
            else {
                return Sales::all();
            }
        }
    }

    public function index(Request $request)
    {
        if($request->input('date1')) {
            $fromDate = Carbon::parse($request->input('date1'),env('TIME_ZONE'))->format('Y-m-d');
            $toDate = Carbon::parse($request->input('date2'),env('TIME_ZONE'))->format('Y-m-d');
        }else{
            $fromDate=0;
            $toDate=0;
        }

        if(strlen($request->input('sage'))>0) {
            $sage = $request->input('sage');
        }else{
            $sage=0;
        }

        if(strlen($request->input('customer'))>0) {
            $customer = $request->input('customer');
            if(Customers::where('customer_account',$customer)->count()) {
                $customerName = Customers::where('customer_account', $customer)->first()->customer_account;
            }else{
                $customerName='none';
            }
        }else{
            $customer=0;
            $customerName=0;
        }
        if(strlen($request->input('invoice_no'))>0) {
            $invoice_no = $request->input('invoice_no');
            if($invoice_no > 0)
                $invoiceNo = Invoices::where('invoice_number',$invoice_no)->first()->invoice_number;
        }else{
            $invoice_no=0;
            $invoiceNo=0;
        }

        return view('admin.sales.exports',compact('customer','customerName','invoice_no','invoiceNo','fromDate','toDate','sage'));
    }
    /**
     * Load invoices data for view table
     * @return mixed
     */
    public function getdataExport($fromdate=null,$todate=null,$customer=null,$invoice=null,$sage=null)
    {
        $this->timeExtension();
        $sales = $this->queries2($fromdate,$todate,$customer,$invoice,$sage );

        return Datatables::of($sales)
            ->addColumn('checkbox', function($sales) use($customer){
                if($customer){
                    $checked = 'checked';
                }else{
                    $checked = '';
                }
                return '<input type="checkbox" name="checkbox[]" id="box-'. $sales->sales_id .'" class="check-style filled-in blue"  onclick="toggleBtn()" value="'.$sales->sales_id.'" '.$checked.'> 
                <label for="box-'. $sales->sales_id .'" class="checkinner"></label>';
            })
            ->editColumn('job_date', function($sales){
                if($sales->job_date)
                    return Carbon::parse($sales->job_date,config('timezone'))->format('d-m-Y');
            })
            ->addColumn('invoice', function($sales){
                return '<a href="'.route('invoices.preview',['account'=>$sales->customer_account,'invno'=>$sales->invoice_number,'date'=>$sales->invoice_date,'printer'=>0]).'" class="btn btn-outline-info btn-xs">invoice</a>';
            })
            ->rawColumns(['invoice','checkbox'])->make(true);
    }


    public function invoiceAuto(Request $request){
        $term = $request->input('searchText');
        $results = array();
        $queries = DB::table('invoices')
            ->where('invoice_number', 'LIKE', '%' . $term . '%')
            ->limit(10)->get();

        foreach ($queries as $query)
        {
            $results[] = [
                'id' => $query->invoice_id,
                'value' => $query->invoice_number,
            ];
        }
        return response()->json($results);
    }


    /**
     * Export to csv and excel
     * @param $type
     * @return mixed
     */

    public function csvExports($type, $fromdate=null, $todate=null,$customer=null,$invoice=null,$sage=null){
        $this->timeExtension();
        $sales = $this->queries($fromdate,$todate,$customer,$invoice,$sage);
        $paymentsArray = [];
        if($sage){
            $paymentsArray[] = ['customer_account2', 'invoice_date', 'job_number', 'net_total','invoice_total'];
            //$paymentsArray[] = ['invoice_number', 'customer_account2', 'invoice_date', 'job_number', 'invoice_total', 'vat_amount', 'percentage_fuel_surcharge','percentage_resourcing_surcharge','net_total','invoice_total'];
            foreach ($sales as $payment) {
                $paymentsArray[] = [$payment->customer_account2, $payment->invoice_date, $payment->job_number, $payment->net_total,$payment->invoice_total];
                //$paymentsArray[] = [$payment->invoice_number, $payment->customer_account2, $payment->invoice_date, $payment->job_number, $payment->invoice_total, $payment->vat_amount, $payment->percentage_fuel_surcharge, $payment->net_total,$payment->invoice_total];
            }
        }else {
            $paymentsArray[] = ['invoice_number', 'customer_account', 'invoice_date', 'job_number', 'sender_reference', 'postcode2', 'destination', 'service_type', 'items2', 'volume_weight', 'sub_total', 'percentage_fuel_surcharge','percentage_resourcing_surcharge', 'vat_percent','invoice_total'];
            foreach ($sales as $payment) {
                $paymentsArray[] = [$payment->invoice_number, $payment->customer_account, $payment->invoice_date, $payment->job_number, $payment->sender_reference, $payment->postcode2, $payment->destination, $payment->service_type, $payment->items2, $payment->volume_weight, $payment->sub_total, $payment->percentage_fuel_surcharge, $payment->vat_percent,$payment->invoice_total];
            }
        }
        //generate report
        return (new InvoiceCustomExports([$paymentsArray]))->download('sales_data_'.rand(1,9999).'.'.$type);
    }

    /**
     * Export Selected to csv/sage
     */
    public function csvExportSelected(Request $request){
        $requestData = $request->input('checkbox',[]);
        if(count($requestData)>0) {
            $paymentsArray = [];
                $sales = Sales::select(
                    'invoice_number', 'customer_account2',
                    'invoice_date', 'job_number', 'invoice_total', 'vat_amount',
                    'percentage_fuel_surcharge','percentage_resourcing_surcharge',
                    DB::raw('ROUND((invoice_total-vat_amount),2) AS net_total')
                )
                    ->where('customer_account2','!=','')
                    ->whereIn('sales_id', $requestData)
                    ->groupBy('invoice_number')
                    ->orderBy('customer_account','asc')
                    ->get();
                //dd(count($sales)) ;
            $paymentsArray[] = ['customer_account2', 'invoice_date', 'job_number', 'net_total','invoice_total'];
            foreach ($sales as $payment) {
                $paymentsArray[] = [$payment->customer_account2, $payment->invoice_date, $payment->job_number, $payment->net_total,$payment->invoice_total];
            }
            //generate sage report
            return (new InvoiceCustomExports([$paymentsArray]))->download('sales_data_'.rand(1,9999).'.csv');
        }
    }


}


