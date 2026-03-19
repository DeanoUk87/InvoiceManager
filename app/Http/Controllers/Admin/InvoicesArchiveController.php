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
use App\Exports\InvoiceCustomExportsString;
use App\Http\Controllers\Traits\CustomQueryArchive as CustomQuery;
use App\Http\Controllers\Traits\Uploader;

use App\Jobs\SendMailJob;
use App\Mail\MassMail;
use App\Models\Customers;
use App\Models\InvoicesArchive as Invoices;

use App\Models\SalesArchive as Sales;
use App\Models\Settings;
use App\Models\System\Upload;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel;
use Session;
use Validator;
use Auth;
use Yajra\Datatables\Datatables;
use PDF;
use DB;
use Image;

class InvoicesArchiveController extends Controller
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
    public function queries($fromdate=null,$todate=null,$customer=null,$invoice=null,$printer=null)
    {
        if(Auth::user()->hasAnyRole(['admin','admin2'])) {
            if (($fromdate || $customer || $invoice) and $printer) {
                return Invoices::where('customer_account', $customer)
                    ->orWhere('invoice_number', $invoice)
                    ->orWhere('printer', $printer)
                    ->orWhereBetween(DB::raw("DATE(invoice_date)"), [$fromdate, $todate]);
            }
            elseif ($printer and !$fromdate and !$customer and !$invoice) {
                return Invoices::where('printer', $printer);
            }
            else {
                return Invoices::query();
            }
        }else{
            $customer = $this->memberInfo()->username;
            if ($fromdate  || $invoice) {
                return Invoices::where('invoice_number', $invoice)
                    ->orWhereBetween(DB::raw("DATE(invoice_date)"), [$fromdate, $todate])
                    ->where(function ($query) use ($customer){
                        $query->where('customer_account', $customer);
                    });
            }
            else {
                return Invoices::where('customer_account', $customer)
                    ;
            }
        }
    }

    public function index(Request $request)
    {
        if(isset($_GET['date1'])) {
            $fromDate = Carbon::parse($_GET['date1'],env('TIME_ZONE'))->format('Y-m-d');
            $toDate = Carbon::parse($_GET['date2'],env('TIME_ZONE'))->format('Y-m-d');
        }else{
            $fromDate=0;
            $toDate=0;
        }

        if(isset($_GET['customer']) and strlen($_GET['customer'])>0) {
            $customer = $_GET['customer'];
            //if($customer > 0)
            $customerx = Customers::where('customer_account',$customer)->first();
            if($customerx) {
                $customerName = $customerx->customer_account;
            }else{
                $customerName=0;
            }
        }else{
            $customer=0;
            $customerName=0;
        }
        if(isset($_GET['invoice_no']) and strlen($_GET['invoice_no'])>0) {
            $invoice_no = $_GET['invoice_no'];
            if($invoice_no > 0)
                $invoice = Invoices::where('invoice_number',$invoice_no)->first();
            if($invoice) {
                $invoiceNo = $invoice->invoice_number;
            }else{
                $invoiceNo=0;
            }
        }else{
            $invoice_no=0;
            $invoiceNo=0;
        }
        if(isset($_GET['printer']) and strlen($_GET['printer'])>0) {
            $printer = $_GET['printer'];
        }else{
            $printer=0;
        }
        $invoice=Invoices::count();
        return view('admin.archive.invoices.index',compact('customer','customerName','invoice_no','invoiceNo','fromDate','toDate','printer','invoice'));
    }
    /**
     * Load invoices data for view table
     * @return mixed
     */
    public function getdata($fromdate=null,$todate=null,$customer=null,$invoice=null,$printer=null)
    {
        //$invoices = Invoices::all();
        $invoices = $this->queries($fromdate,$todate,$customer,$invoice,$printer);

        return Datatables::of($invoices)
            ->addColumn('checkbox', function($invoices){
                return '<input type="checkbox" name="checkbox[]" id="box-'. $invoices->invoice_id .'" class="check-style filled-in blue"  onclick="toggleBtn()" value="'.$invoices->invoice_id.'"> 
                <label for="box-'. $invoices->invoice_id .'" class="checkinner"></label>';
            })
            ->editColumn('invoice_date', function($invoices){
                if($invoices->invoice_date)
                    return Carbon::parse($invoices->invoice_date,config('timezone'))->format('d-m-Y');
            })
            ->editColumn('due_date', function($invoices){
                if($invoices->due_date)
                    return Carbon::parse($invoices->due_date,config('timezone'))->format('d-m-Y');
            })
            ->editColumn('printer', function($invoices){
                if($invoices->printer==2) {
                    return '<a href="' . route('archive-invoices.preview', ['account' => $invoices->customer_account, 'invno' => $invoices->invoice_number, 'date' => $invoices->invoice_date,'printer'=>0]) . '" class="btn btn-success btn-xs">Printed</a>';
                }else{
                    return '<a href="' . route('archive-invoices.preview', ['account' => $invoices->customer_account, 'invno' => $invoices->invoice_number, 'date' => $invoices->invoice_date,'printer'=>1]) . '" class="btn btn-outline-danger btn-xs">Print Invoice</a>';
                }
            })
            ->addColumn('invoice', function($invoices){
                return '<a href="'.route('archive-invoices.preview',['account'=>$invoices->customer_account,'invno'=>$invoices->invoice_number,'date'=>$invoices->invoice_date,'printer'=>0]).'" class="btn btn-outline-info btn-xs">invoice</a>';
            })
            ->addColumn('action', function($invoices){
                return '
           <div class="btn-group btn-group-xs" role="group" aria-label="actions"> 
         
           <a href="javascript:viod(0)" data-id="row-'. $invoices->invoice_id .'" onclick="deleteData(\''.url('admin/archive-invoices/delete').'\','.$invoices->invoice_id.')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a> 
           </div>';
            })
            ->rawColumns(['checkbox','action','invoice','printer'])->make(true);
    }

    public function sendPreview(){
        $invoices = Customers::join('invoices','invoices.customer_account','=','customers.customer_account')
            ->where('customers.customer_email','!=','')
            ->where('email_status',0)
            ->count();
        $limitInv = Settings::limit(1)->first()->send_limit;
        if($limitInv){
            $limit = $limitInv;
        }else{
            $limit = 50;
        }
        return view('admin.archive.invoices.send-preview', compact('invoices','limit'));
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
     * This method delete record from database
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id){
        if ( $request->ajax() ) {
            Invoices::findOrFail($id)->delete();
            return response()->json(['success' => true, 'message' => trans('app.delete.success')]);
        }
        return response()->json(['error' => true, 'message' => trans('app.delete.error')]);
    }

    /**
     * This method handle file delete from related table which were uploaded using the multiple upload option.
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyFile2(Request $request, $id) {
        if ( $request->ajax() ) {
            $this->deleteFileWith($id);
            return response()->json(['success' => true, 'message' => trans('app.delete.success')]);
        }
        return response()->json(['error' => true, 'message' => trans('app.delete.error')]);
    }

    /**
     * Delete with checkbox
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletemulti(Request $request){
        $requestData = $request->input('checkbox',[]);
        if(count($requestData)>0) {
            foreach ($requestData as $id) {
                Invoices::where('invoice_id', $id)->delete();
            }
            return response()->json(['success' => 'delete', 'message' => trans('app.delete.success')]);
        }
        return response()->json(['error' => true, 'message' => trans('app.delete.error')]);
    }

    /**
     * Export to PDF
     * @param Request $request
     * @return mixed
     */
    public function exportPDF($fromdate=null,$todate=null,$customer=null,$invoice=null){
        $invoices = $this->queries($fromdate,$todate,$customer,$invoice);
        $pdf = PDF::loadView('admin.archive.invoices.print', compact('invoices'));
        return $pdf->download('invoices_data.pdf');
        /* //return $pdf->stream('invoices_data.pdf'); //print to browser */
    }
    /**
     * Export to csv and excel
     * @param $type
     * @return mixed
     */
    public function exportFile($type){
        $invoices = Invoices::select('sales_id','customer_account','invoice_number','invoice_date','due_date','date_created','terms','printer','po_number','num')->limit(1000)->toArray();
        return Excel::create('invoices_data', function($excel) use ($invoices) {
            $excel->sheet('Invoices Data', function($sheet) use ($invoices)
            {
                $sheet->fromArray($invoices);
            });
        })->download($type);
    }


    /*Invoice Process*/
    //GROUP ACCOUNT IN SALES
    public function SelectAllGroup(Request $request, $date=null)
    {
        $customers = Sales::where('ms_created',0)
            ->groupBy('invoice_number')
            ->orderBy('sales_id')
            ->get();
        if(count($customers) > 0){
            foreach($customers as $prof){
                $customer = $this->GetCustomerName($prof->customer_account, $prof->invoice_number,$prof->invoice_date);
                $this->InsertInvoice($prof->customer_account,$prof->invoice_number,$prof->invoice_date,Carbon::parse(config('timezone',$prof->invoice_date))->addDays($prof->numb2),Carbon::now(config('timezone'))->format('Y-m-d'),'',$prof->sales_id, $customer->po_number);
            }
            return redirect()->route('archive.invoices.index')->with('success', 'All new invoices has been generated successfully!');
        }
        else{
            return redirect()->route('archive.invoices.index')->with('error', 'No new invoices found!');
        }
    }

    /*Mass Invoice Email*/
    public function invoice($customer_account, $invoice_number,$invoice_date,$print=null){
        $setup = $this->AppSettings();
        $customers = $this->GetEmailAccountsMassInvoice();
        $prof = $this->GetCustomerName($customer_account, $invoice_number,$invoice_date);
        $owner = Customers::where('customer_account',$customer_account)->first();
        $items = $this->ListItems($customer_account,$invoice_date,$invoice_number);
        $subtotal = $this->SumSubTotalMassInvoice($customer_account,'sub_total');
        $vat = $this->SumSubTotalMassInvoice($customer_account,'vat_percent');
        $invo = Invoices::where('customer_account',$customer_account)
            ->where('invoice_date',$invoice_date)
            ->where('invoice_number',$invoice_number)->limit(1)->first();
        $mail = $this->SelectOneAccount($customer_account);
        if($print==1) {
            DB::table('invoices')->where('printer', 1)
                ->where('customer_account',$customer_account)
                ->where('invoice_date',$invoice_date)
                ->where('invoice_number',$invoice_number)
                ->update(['printer' => 2]);
            DB::table('sales')->where('ms_created', 0)
                ->where('customer_account',$customer_account)
                ->where('invoice_date',$invoice_date)
                ->where('invoice_number',$invoice_number)
                ->update(['ms_created' => 1]);
            $printer =1;
        }else{
            $printer =0;
        }
        return view('admin.archive.invoices.invoice',compact('setup','customers','prof','items','subtotal','vat','customer_account','invoice_number','invoice_date','invo','mail','printer','owner'));
    }

    public function exportDetailPDF($customer_account, $invoice_number,$invoice_date){
        //$invoices = Invoices::findOrFail($id);
        ini_set("pcre.backtrack_limit", "5000000");

        $setup = $this->AppSettings();
        $customers = $this->GetEmailAccountsMassInvoice();
        $prof = $this->GetCustomerName($customer_account, $invoice_number,$invoice_date);
        $items = $this->ListItems($customer_account,$invoice_date,$invoice_number);
        $subtotal = $this->SumSubTotalMassInvoice($customer_account,'sub_total');
        $vat = $this->SumSubTotalMassInvoice($customer_account,'vat_percent');
        $invo = Invoices::where('customer_account',$customer_account)
            ->where('invoice_date',$invoice_date)
            ->where('invoice_number',$invoice_number)->limit(1)->first();
        $customer = $this->SelectOneAccount($customer_account);
        $owner = Customers::where('customer_account',$customer_account)->first();
        //screen print
        if(request()->input('print')) {
            return view('admin.archive.invoices.print-details', compact('setup', 'customers', 'prof', 'items', 'subtotal', 'vat', 'customer_account', 'invoice_number', 'invoice_date', 'invo', 'customer', 'owner'));
        }else {
            $pdf = PDF::loadView('admin.archive.invoices.print-invoice', compact('setup', 'customers', 'prof', 'items', 'subtotal', 'vat', 'customer_account', 'invoice_number', 'invoice_date', 'invo', 'customer', 'owner'));
            $pdf->setPaper('A4');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4');
            //$pdf->setPaper(array(0,0,820.78,600.94),'A4');
            return $pdf->download('invoice_' . $invoice_number . '.pdf');
        }
    }
    /*Ecport to excel*/
    public function exportDetailExcel($customer_account, $invoice_number,$invoice_date){
        $items = $this->ListItems($customer_account,$invoice_date,$invoice_number);
        $paymentsArray = [];
        $paymentsArray[] = ['JOB DATE', 'INVOICE NUMBER', 'JOB NUMBER', 'SENDERS REF', 'POSTCODE', 'DESTINATION', 'INVOICE DATE','TOWN/CITY', 'SERVICE TYPE', 'ITEMS','WEIGHT','CHARGE','INVOICE TOTAL'];
        foreach ($items as $payment) {
            $paymentsArray[] = [$payment->job_date,$payment->invoice_number, $payment->job_number, $payment->sender_reference, $payment->postcode2, $payment->destination, $payment->invoice_date, $payment->town2, $payment->service_type, $payment->items2, $payment->volume_weight, $payment->sub_total, $payment->invoice_total];
        }
        return (new InvoiceCustomExports([$paymentsArray]))
            ->download('Invoice_'.$invoice_number.'.csv');
    }

    /*
    * Invoice Email to Customer
    *
    * */
    public function sendInvoiceToMail(Request $request){
        $customer_account = $request->input('customer_account');
        $invoice_number = $request->input('invoice_number');
        $invoice_date = $request->input('invoice_date');
        $customer_email = $request->input('customer_email');
        $customer_email_bcc = $request->input('customer_email_bcc');

        if($customer_email){
            if(strpos($customer_email, ',') === false){
                if(filter_var($customer_email, FILTER_VALIDATE_EMAIL)){
                    Mail::to($customer_email)->send(new MassMail($customer_account,$invoice_number,$invoice_date,$customer_email,$customer_email_bcc));
                } else{
                    return back()->withInput()->with('error', 'Invalid Email Address!');
                }
                return back()->withInput()->with('success', 'Invoice '.$invoice_number.' has been sent to customer email '.$customer_email);
            } else {
                $custEmails = explode(',',$customer_email);
                foreach($custEmails as $customer_email){
                    if(filter_var($customer_email, FILTER_VALIDATE_EMAIL)){
                        Mail::to($customer_email)->send(new MassMail($customer_account,$invoice_number,$invoice_date,$customer_email,$customer_email_bcc));
                    } else{
                        return back()->withInput()->with('error', 'Invalid Email Address!');
                    }
                }
                return back()->withInput()->with('success', 'Invoice '.$invoice_number.' has been sent to customer email');
            }
        }else{
            return redirect()->route('archive.invoices.index')->with('error', 'Email seems to be empty!');
        }
    }

    /*
     * Mass Invoice Email
     *
     * */
    public function massMail(){
        $customers = $this->GetEmailAccountsMassInvoice();
        if(count($customers) > 0){
            $batch = Str::random(10);
            DB::table('jobs_batch')->insert(['batch_no'=>$batch, 'created_at' => Carbon::now()]);
            DB::table('invoices')->where('email_status',0)->update(['batch_no'=>$batch]);
            Customers::join('invoices','invoices.customer_account','=','customers.customer_account')
                ->where('customers.customer_email','!=','')
                ->where('email_status',0)
                ->count();
            return redirect()->route('archive.send.preview',['sending'=>1])->with('success', 'Now Sending...');
        }else{
            return redirect()->route('archive.invoices.index')->with('error', 'No new invoices found!');
        }
    }
}


