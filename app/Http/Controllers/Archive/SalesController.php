<?php
/*
* =======================================================================
* FILE NAME:        SalesController.php
* DATE CREATED:  	17-01-2019
* FOR TABLE:  		sales
* AUTHOR:			DH Fusion Ltd.
* CONTACT:			http://dhfusion.co.uk <info@dhfusion.co.uk>
* =======================================================================
*/
namespace App\Http\Controllers\Archive;
use App\Http\Controllers\Traits\CustomQuery2;
use App\Http\Controllers\Traits\Uploader;

use App\Models\Archive\Invoices;
use App\Models\Archive\Sales;

use App\Models\System\Upload;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Session;
use Validator;
use Auth;
use Yajra\Datatables\Datatables;
use PDF;
use DB;
use Excel;
use Image;

class SalesController extends Controller
{
    use Uploader, CustomQuery2;

    /**
     * SalesController constructor.
     */
    public function __construct() {
        $this->middleware(['auth', 'verifier']);
    }
    /**
     * This method display sales view for datatable
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if(isset($_GET['type']) and strlen($_GET['type'])>0) {
            $type = $_GET['type'];
        }else{
            $type='';
        }
        return view('archive.sales.index',compact('type'));
    }
    /**
     * Load sales data for view table
     * @return mixed
     */
    public function getdata()
    {
        $this->timeExtension();

        if(Auth::user()->hasAnyRole(['admin','admin2'])) {
            $sales = Sales::query();
        }else{
            $customer = $this->memberInfo()->username;
            $sales =  Sales::where('customer_account', $customer);
        }
        return Datatables::of($sales)
            ->addColumn('checkbox', function($sales){
                return '<input type="checkbox" name="checkbox[]" id="box-'. $sales->sales_id .'" class="check-style filled-in blue"  onclick="toggleBtn()" value="'.$sales->sales_id.'"> 
                <label for="box-'. $sales->sales_id .'" class="checkinner"></label>';
            })
            ->editColumn('job_date', function($sales){
                if($sales->job_date)
                    return Carbon::parse($sales->job_date,config('timezone'))->format('d-m-Y');
            })
            ->addColumn('invoice', function($sales){
                return '<a href="'.route('archive.invoices.preview',['account'=>$sales->customer_account,'invno'=>$sales->invoice_number,'date'=>$sales->invoice_date,'printer'=>0]).'" class="btn btn-outline-info btn-xs">invoice</a>';
            })
            ->addColumn('action', function($sales){
                return '
           <div class="btn-group btn-group-xs" role="group" aria-label="actions"> 
           <a href="javascript:viod(0)" data-id="row-'. $sales->sales_id .'" onclick="viewDetails(\''.url('archive/sales/details').'\','.$sales->sales_id.')" class="btn btn-info btn-xs"><i class="fa fa-eye"></i></a> 
           <a href="javascript:viod(0)" data-id="row-'. $sales->sales_id .'" onclick="editForm(\''.url('archive/sales/edit').'\','.$sales->sales_id.')" class="btn btn-success btn-xs"><i class="fa fa-pencil"></i></a> 
           <a href="javascript:viod(0)" data-id="row-'. $sales->sales_id .'" onclick="deleteData(\''.url('archive/sales/delete').'\','.$sales->sales_id.')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a> 
           </div>';
            })
            ->rawColumns(['checkbox','action','invoice'])->make(true);
    }

    /**
     * This method select sales details
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function details($id){
        $sales = Sales::findOrFail($id);
        return view('archive.sales.details', compact('sales'));
    }

    /**
     * This method load sales form
     * @return mixed
     */
    public function insert(){
        return view('archive.sales.create' );
    }

    public function store(Request $request){
        /* validate sales data */
        $validator = Validator::make($request->all(),
            [
                'invoice_number' => 'required',
                'invoice_date' => 'required',
                'customer_account' => 'required',
                'job_number' => 'required',

            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => true,'message'=>$validator->errors()->all()]);
        }
        else {
            /* get post data */
            $data = array(
                'invoice_number' => $request->input('invoice_number'),
                'invoice_date' => $request->input('invoice_date'),
                'customer_account' => $request->input('customer_account'),
                'customer_name' => $request->input('customer_name'),
                'address1' => $request->input('address1'),
                'address2' => $request->input('address2'),
                'town' => $request->input('town'),
                'country' => $request->input('country'),
                'postcode' => $request->input('postcode'),
                'spacer1' => $request->input('spacer1'),
                'customer_account2' => $request->input('customer_account2'),
                'numb1' => $request->input('numb1'),
                'items' => $request->input('items'),
                'weight' => $request->input('weight'),
                'invoice_total' => $request->input('invoice_total'),
                'numb2' => $request->input('numb2'),
                'spacer2' => $request->input('spacer2'),
                'job_number' => $request->input('job_number'),
                'job_date' => $request->input('job_date'),
                'sending_deport' => $request->input('sending_deport'),
                'delivery_deport' => $request->input('delivery_deport'),
                'destination' => $request->input('destination'),
                'town2' => $request->input('town2'),
                'postcode2' => $request->input('postcode2'),
                'service_type' => $request->input('service_type'),
                'items2' => $request->input('items2'),
                'volume_weight' => $request->input('volume_weight'),
                'numb3' => $request->input('numb3'),
                'increased_liability_cover' => $request->input('increased_liability_cover'),
                'sub_total' => $request->input('sub_total'),
                'spacer3' => $request->input('spacer3'),
                'numb4' => $request->input('numb4'),
                'sender_reference' => $request->input('sender_reference'),
                'numb5' => $request->input('numb5'),
                'percentage_fuel_surcharge' => $request->input('percentage_fuel_surcharge'),
                'spacer4' => $request->input('spacer4'),
                'senders_postcode' => $request->input('senders_postcode'),
                'vat_amount' => $request->input('vat_amount'),
                'vat_percent' => $request->input('vat_percent'),
                'uploadcode' => $request->input('uploadcode'),
                'ms_created' => $request->input('ms_created'),
                'job_dat' => $request->input('job_dat'),

            );
            /* insert post data */
            $data= Sales::create($data);

            /* return json message */
            return response()->json(['success' => true,'message' => trans('app.add.success')]);
        }
    }

    /**
     * Select sales edit
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id){

        $sales = Sales::findOrFail($id);
        /* pass sales data to view and load list view */
        return view('archive.sales.edit', compact('sales' ));
    }

    /**
     * This method process sales edit form
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request){
        /* validate sales data */
        $validator = Validator::make($request->all(),
            [
                'invoice_number' => 'required',
                'invoice_date' => 'required',
                'customer_account' => 'required',
                'job_number' => 'required',

            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => true,'message'=>$validator->errors()->all()]);
        }
        else {
            $sales = Sales::findOrFail($id);
            $sales->invoice_number = $request->input('invoice_number');
            $sales->invoice_date = $request->input('invoice_date');
            $sales->customer_account = $request->input('customer_account');
            $sales->customer_name = $request->input('customer_name');
            $sales->address1 = $request->input('address1');
            $sales->address2 = $request->input('address2');
            $sales->town = $request->input('town');
            $sales->country = $request->input('country');
            $sales->postcode = $request->input('postcode');
            $sales->spacer1 = $request->input('spacer1');
            $sales->customer_account2 = $request->input('customer_account2');
            $sales->numb1 = $request->input('numb1');
            $sales->items = $request->input('items');
            $sales->weight = $request->input('weight');
            $sales->invoice_total = $request->input('invoice_total');
            $sales->numb2 = $request->input('numb2');
            $sales->spacer2 = $request->input('spacer2');
            $sales->job_number = $request->input('job_number');
            $sales->job_date = $request->input('job_date');
            $sales->sending_deport = $request->input('sending_deport');
            $sales->delivery_deport = $request->input('delivery_deport');
            $sales->destination = $request->input('destination');
            $sales->town2 = $request->input('town2');
            $sales->postcode2 = $request->input('postcode2');
            $sales->service_type = $request->input('service_type');
            $sales->items2 = $request->input('items2');
            $sales->volume_weight = $request->input('volume_weight');
            $sales->numb3 = $request->input('numb3');
            $sales->increased_liability_cover = $request->input('increased_liability_cover');
            $sales->sub_total = $request->input('sub_total');
            $sales->spacer3 = $request->input('spacer3');
            $sales->numb4 = $request->input('numb4');
            $sales->sender_reference = $request->input('sender_reference');
            $sales->numb5 = $request->input('numb5');
            $sales->percentage_fuel_surcharge = $request->input('percentage_fuel_surcharge');
            $sales->spacer4 = $request->input('spacer4');
            $sales->senders_postcode = $request->input('senders_postcode');
            $sales->vat_amount = $request->input('vat_amount');
            $sales->vat_percent = $request->input('vat_percent');
            $sales->uploadcode = $request->input('uploadcode');
            $sales->ms_created = $request->input('ms_created');
            $sales->job_dat = $request->input('job_dat');


            $sales->save();

            return response()->json(['success' => true,'message' => trans('app.update.success')
            ]);
        }
    }

    /**
     * This method delete record from database
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id){
        if ( $request->ajax() ) {
            Sales::findOrFail($id)->delete();
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
                Sales::where('sales_id', $id)->delete();
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
    public function exportPDF(Request $request){
        $sales = Sales::all();
        $pdf = PDF::loadView('archive.sales.print', compact('sales'));
        return $pdf->download('sales_data.pdf');
        /* //return $pdf->stream('sales_data.pdf'); //print to browser */
    }

    public function exportDetailPDF($id){
        $sales = Sales::findOrFail($id);
        $pdf = PDF::loadView('archive.sales.print-details', compact('sales'));
        return $pdf->download('sales_data_details.pdf');
    }

    /**
     * Export to csv and excel
     * @param $type
     * @return mixed
     */
    public function exportFile($type){
        $sales = Sales::all('invoice_number','invoice_date','customer_account','customer_name','address1','address2','town','country','postcode','spacer1','customer_account2','numb1','items','weight','invoice_total','numb2','spacer2','job_number','job_date','sending_deport','delivery_deport','destination','town2','postcode2','service_type','items2','volume_weight','numb3','increased_liability_cover','sub_total','spacer3','numb4','sender_reference','numb5','percentage_fuel_surcharge','spacer4','senders_postcode','vat_amount','vat_percent','uploadcode','ms_created','job_dat')->toArray();
        return Excel::create('sales_data', function($excel) use ($sales) {
            $excel->sheet('Sales Data', function($sheet) use ($sales)
            {
                $sheet->fromArray($sales);
            });
        })->download($type);
    }

    public function truncateTable(){
        Sales::truncate();
        Invoices::truncate();
        return redirect()->route('sales.index')->with('success', 'All data have been cleared!');
    }

}
	