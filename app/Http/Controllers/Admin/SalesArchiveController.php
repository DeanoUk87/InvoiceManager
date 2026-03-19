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
namespace App\Http\Controllers\Admin;
use App\Exports\SalesExports;
use App\Http\Controllers\Traits\CustomQuery;
use App\Http\Controllers\Traits\Uploader;

use App\Models\InvoicesArchive as Invoices;
use App\Models\SalesArchive as Sales;

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

class SalesArchiveController extends Controller
{
    use Uploader, CustomQuery;

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
        if($request->input('type') and strlen($request->input('type'))>0) {
            $type = $request->input('type');
        }else{
            $type='';
        }
        if($request->input('date1')) {
            $fromDate = Carbon::parse($request->input('date1'),env('TIME_ZONE'))->format('Y-m-d');
            $toDate = Carbon::parse($request->input('date2'),env('TIME_ZONE'))->format('Y-m-d');
        }else{
            $fromDate=0;
            $toDate=0;
        }
        return view('admin.archive.sales.index',compact('type','fromDate','toDate'));
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
                return '<a href="'.route('archive-invoices.preview',['account'=>$sales->customer_account,'invno'=>$sales->invoice_number,'date'=>$sales->invoice_date,'printer'=>0]).'" class="btn btn-outline-info btn-xs">invoice</a>';
            })
            ->addColumn('action', function($sales){
                return '
           <div class="btn-group btn-group-xs" role="group" aria-label="actions"> 
           <a href="javascript:viod(0)" data-id="row-'. $sales->sales_id .'" onclick="viewDetails(\''.url('admin/archive-sales/details').'\','.$sales->sales_id.')" class="btn btn-info btn-xs"><i class="fa fa-eye"></i></a> 
           <a href="javascript:viod(0)" data-id="row-'. $sales->sales_id .'" onclick="deleteData(\''.url('admin/archive-sales/delete').'\','.$sales->sales_id.')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a> 
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
        return view('admin.archive.sales.details', compact('sales'));
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
     * Export to csv and excel
     * @param $type
     * @return mixed
     */
    public function exportFile($type){
        return (new SalesExports())->download('sales.'.$type);
    }

    public function truncateTable(){
        Sales::truncate();
        Invoices::truncate();
        return redirect()->route('archive-sales.index')->with('success', 'All data have been cleared!');
    }
}
