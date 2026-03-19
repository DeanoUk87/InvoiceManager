<?php
/*
* =======================================================================
* FILE NAME:        CustomersController.php
* DATE CREATED:  	17-01-2019
* FOR TABLE:  		customers
* AUTHOR:			DH Fusion Ltd.
* CONTACT:			http://dhfusion.co.uk <info@dhfusion.co.uk>
* =======================================================================
*/
namespace App\Http\Controllers\Archive;
use App\Http\Controllers\Traits\Uploader;

use App\Models\Archive\Customers;

use App\Models\System\Upload;
use App\Http\Controllers\Controller;
use App\User;
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

class CustomersController extends Controller
{
    use Uploader;

    /**
     * CustomersController constructor.
     */
    public function __construct() {
        $this->middleware(['auth', 'verifier']);
    }
    /**
     * This method display customers view for datatable
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('admin.customers.index');
    }
    /**
     * Load customers data for view table
     * @return mixed
     */
    public function getdata()
    {
        $customers = Customers::query();
        return Datatables::of($customers)
            ->addColumn('checkbox', function($customers){
                return '<input type="checkbox" name="checkbox[]" id="box-'. $customers->contact_id .'" class="check-style filled-in blue"  onclick="toggleBtn()" value="'.$customers->contact_id.'"> 
                <label for="box-'. $customers->contact_id .'" class="checkinner"></label>';
            })
            ->editColumn('access', function($invoices){
                if(User::where('username',$invoices->customer_account)->count()>0) {
                    return '<a href="javascript:viod(0)" class="btn btn-success btn-xs">Can Access</a>';
                }else{
                    return '<a href="' . route('customers.login', ['id' => $invoices->contact_id]) . '" class="btn btn-outline-danger btn-xs">No Access</a>';
                }
            })
            ->addColumn('action', function($customers){
                return '
           <div class="btn-group btn-group-xs" role="group" aria-label="actions"> 
           <a href="javascript:viod(0)" data-id="row-'. $customers->contact_id .'" onclick="viewDetails(\''.url('admin/customers/details').'\','.$customers->contact_id.')" class="btn btn-info btn-xs"><i class="fa fa-eye"></i></a> 
           <a href="javascript:viod(0)" data-id="row-'. $customers->contact_id .'" onclick="editForm(\''.url('admin/customers/edit').'\','.$customers->contact_id.')" class="btn btn-success btn-xs"><i class="fa fa-pencil"></i></a> 
           <a href="javascript:viod(0)" data-id="row-'. $customers->contact_id .'" onclick="deleteData(\''.url('admin/customers/delete').'\','.$customers->contact_id.')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a> 
           </div>';
            })
            ->rawColumns(['checkbox','action','access'])->make(true);
    }

    /**
     * This method select customers details
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function details($id){
        $customers = Customers::findOrFail($id);
        return view('admin.customers.details', compact('customers'));
    }

    public function customerAuto(Request $request){
        $term = $request->input('searchText');
        $results = array();
        $queries = DB::table('customers')
            ->where('customer_account', 'LIKE', '%' . $term . '%')
            ->limit(10)->get();

        foreach ($queries as $query)
        {
            $results[] = [
                'id' => $query->contact_id,
                'value' => $query->customer_account,
            ];
        }
        return response()->json($results);
    }

    /**
     * This method load customers form
     * @return mixed
     */
    public function insert(){

        return view('admin.customers.create' );
    }

    public function store(Request $request){
        /* validate customers data */
        $validator = Validator::make($request->all(),
            [
                'customer_account' => 'required',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => true,'message'=>$validator->errors()->all()]);
        }
        else {
            /* get post data */
            $data = array(
                'user_id' => $this->memberId(),
                'customer_account' => $request->input('customer_account'),
                'customer_email' => $request->input('customer_email'),
                'customer_phone' => $request->input('customer_phone'),
                'terms_of_payment' => $request->input('terms_of_payment'),
                'message_type' => $request->input('message_type'),
                'po_number' => $request->input('po_number'),
            );
            /* insert post data */
            $data= Customers::create($data);
            /* return json message */
            return response()->json(['success' => true,'message' => trans('app.add.success')]);
        }
    }

    public function LoginAccess($id){
        $customers = Customers::findOrFail($id);
        return view('admin.customers.login', compact('customers'));
    }

    public function LoginPro(Request $request){
        /* validate customers data */
        $validator = Validator::make($request->all(),
            [
                //'email' => 'required|string|email|max:50|unique:users',
                'username' => 'required|string|max:20|unique:users',
                'password' => 'required|string|confirmed'
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => true,'message'=>$validator->errors()->all()]);
        }
        else {
            $user=User::create([
                'username' => $request->input('username'),
                'email' => $request->input('email'),
                'email_verified_at' => $this->dateTime(),
                'password' => $request->input('password') //bcrypt($request->input('password'))
            ]);
            $user->assignRole('user');
            return response()->json(['success' => true,'message' => 'Login account created successfully for '.$request->input('username')]);
        }
    }


    /**
     * Select customers edit
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id){

        $customers = Customers::findOrFail($id);
        /* pass customers data to view and load list view */
        return view('admin.customers.edit', compact('customers' ));
    }

    /**
     * This method process customers edit form
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request){
        /* validate customers data */
        $validator = Validator::make($request->all(),
            [
                'customer_account' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => true,'message'=>$validator->errors()->all()]);
        }
        else {
            $customers = Customers::findOrFail($id);
            $customers->customer_account = $request->input('customer_account');
            $customers->customer_email = $request->input('customer_email');
            $customers->customer_phone = $request->input('customer_phone');
            $customers->terms_of_payment = $request->input('terms_of_payment');
            $customers->message_type = $request->input('message_type');
            $customers->po_number = $request->input('po_number');
            $customers->save();
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
            Customers::findOrFail($id)->delete();
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
                Customers::where('contact_id', $id)->delete();
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
        $customers = Customers::all();
        $pdf = PDF::loadView('admin.customers.print', compact('customers'));
        return $pdf->download('customers_data.pdf');
        /* //return $pdf->stream('customers_data.pdf'); //print to browser */
    }

    public function exportDetailPDF($id){
        $customers = Customers::findOrFail($id);
        $pdf = PDF::loadView('admin.customers.print-details', compact('customers'));
        return $pdf->download('customers_data_details.pdf');
    }

    /**
     * load import template
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function importExportView(){
        return view('admin.customers.import');
    }

    /**
     * Process imported file
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importFile(Request $request){
        if($request->hasFile('customers_file')){
            $path = $request->file('customers_file')->getRealPath();
            $data = Excel::load($path)->get();
            if($data->count()){
                foreach ($data as $key => $value) {
                    $arr[] = ['user_id' => $value->user_id,'customer_account' => $value->customer_account,'customer_email' => $value->customer_email,'customer_phone' => $value->customer_phone,'terms_of_payment' => $value->terms_of_payment,'message_type' => $value->message_type,'po_number' => $value->po_number];
                }
                if(!empty($arr)){
                    DB::table('customers')->insert($arr);
                    return response()->json(['success' => true, 'message' => trans('app.import.success')]);
                }
            }
        }
        return response()->json(['error' => true, 'message' => trans('app.import.error')]);
    }

    /**
     * Export to csv and excel
     * @param $type
     * @return mixed
     */
    public function exportFile($type){
        $customers = Customers::all('user_id','customer_account','customer_email','customer_phone','terms_of_payment','message_type','po_number')->toArray();
        return Excel::create('customers_data', function($excel) use ($customers) {
            $excel->sheet('Customers Data', function($sheet) use ($customers)
            {
                $sheet->fromArray($customers);
            });
        })->download($type);
    }

    public function truncateTable(){
        Customers::truncate();
        return redirect()->route('customers.index')->with('success', 'All data have been cleared!');
    }

}
	