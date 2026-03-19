<?php
/*
* =======================================================================
* FILE NAME:        MessagesstatusController.php
* DATE CREATED:  	17-04-2019
* FOR TABLE:  		messages_status
* AUTHOR:			Hezecom Technology Solutions LTD.
* CONTACT:			http://hezecom.com <info@hezecom.com>
* =======================================================================
*/
namespace App\Http\Controllers\Admin;
use App\Exports\MessagesstatusExports;
use App\Http\Controllers\Traits\Uploader;

use App\Imports\MessagesstatusImports;
use App\Models\Messagesstatus;

use App\Models\System\Upload;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use Validator;
use Auth;
use Yajra\Datatables\Datatables;
use PDF;
use DB;
use Image;

class MessagesstatusController extends Controller
{
    use Uploader;

    /**
     * MessagesstatusController constructor.
     */
    public function __construct() {
        $this->middleware(['auth', 'verifier']);
    }
    /**
     * This method display messagesstatus view for datatable
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('admin.messagesstatus.index');
    }
    /**
     * Load messagesstatus data for view table
     * @return mixed
     */
    public function getdata()
    {
        $messagesstatus = Messagesstatus::query();
        return Datatables::of($messagesstatus)
            ->addColumn('checkbox', function($messagesstatus){
                return '<input type="checkbox" name="checkbox[]" id="box-'. $messagesstatus->id .'" class="check-style filled-in blue"  onclick="toggleBtn()" value="'.$messagesstatus->id.'"> 
                <label for="box-'. $messagesstatus->id .'" class="checkinner"></label>';
            })
            ->addColumn('action', function($messagesstatus){
                return '
           <div class="btn-group btn-group-xs" role="group" aria-label="actions"> 
           <a href="javascript:viod(0)" data-id="row-'. $messagesstatus->id .'" onclick="viewDetails(\''.url('admin/messagesstatus/details').'\','.$messagesstatus->id.')" class="btn btn-info btn-xs"><i class="fa fa-eye"></i></a> 
           <a href="javascript:viod(0)" data-id="row-'. $messagesstatus->id .'" onclick="editForm(\''.url('admin/messagesstatus/edit').'\','.$messagesstatus->id.')" class="btn btn-success btn-xs"><i class="fa fa-pencil"></i></a> 
           <a href="javascript:viod(0)" data-id="row-'. $messagesstatus->id .'" onclick="deleteData(\''.url('admin/messagesstatus/delete').'\','.$messagesstatus->id.')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a> 
           </div>';
            })
            ->rawColumns(['checkbox','action'])->make(true);
    }

    /**
     * This method select messagesstatus details
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function details($id){
        $messagesstatus = Messagesstatus::findOrFail($id);
        return view('admin.messagesstatus.details', compact('messagesstatus'));
    }

    /**
     * This method load messagesstatus form
     * @return mixed
     */
    public function insert(){

        return view('admin.messagesstatus.create' );
    }

    public function store(Request $request){
        /* validate messagesstatus data */
        $validator = Validator::make($request->all(),
            [
                'sent_at' => 'required',

            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => true,'message'=>$validator->errors()->all()]);
        }
        else {
            /* get post data */
            $data = array(
                'message_id' => $request->input('message_id'),
                'user_id' => $request->input('user_id'),
                'sent_status' => $request->input('sent_status'),
                'sent_at' => $request->input('sent_at'),

            );
            /* insert post data */
            $data= Messagesstatus::create($data);

            /* return json message */
            return response()->json(['success' => true,'message' => trans('app.add.success')]);
        }
    }

    /**
     * Select messagesstatus edit
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id){

        $messagesstatus = Messagesstatus::findOrFail($id);
        /* pass messagesstatus data to view and load list view */
        return view('admin.messagesstatus.edit', compact('messagesstatus' ));
    }

    /**
     * This method process messagesstatus edit form
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request){
        /* validate messagesstatus data */
        $validator = Validator::make($request->all(),
            [
                'sent_at' => 'required',

            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => true,'message'=>$validator->errors()->all()]);
        }
        else {
            $messagesstatus = Messagesstatus::findOrFail($id);
            $messagesstatus->message_id = $request->input('message_id');
            $messagesstatus->user_id = $request->input('user_id');
            $messagesstatus->sent_status = $request->input('sent_status');
            $messagesstatus->sent_at = $request->input('sent_at');


            $messagesstatus->save();

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
            Messagesstatus::findOrFail($id)->delete();
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
                Messagesstatus::where('id', $id)->delete();
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
        $messagesstatus = Messagesstatus::all();
        $pdf = PDF::loadView('admin.messagesstatus.print', compact('messagesstatus'));
        return $pdf->download('messagesstatus_data.pdf');
        /* //return $pdf->stream('messagesstatus_data.pdf'); //print to browser */
    }

    public function exportDetailPDF($id){
        $messagesstatus = Messagesstatus::findOrFail($id);
        $pdf = PDF::loadView('admin.messagesstatus.print-details', compact('messagesstatus'));
        return $pdf->download('messagesstatus_data_details.pdf');
    }

    /**
     * load import template
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function importExportView(){
        return view('admin.messagesstatus.import');
    }

    /**
     * Process imported file
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importFile(Request $request){
        if($request->hasFile('messagesstatus_file')){
            $path = $request->file('messagesstatus_file')->getRealPath();
            Excel::import(new MessagesstatusImports, $path);
            return response()->json(['success' => true, 'message' => trans('app.import.success')]);
        }
        return response()->json(['error' => true, 'message' => trans('app.import.error')]);
    }

    /**
     * Export to csv and excel
     * @param $type
     * @return mixed
     */
    public function exportFile($type){
        return (new MessagesstatusExports)->download('messagesstatus.'.$type);
    }

}
