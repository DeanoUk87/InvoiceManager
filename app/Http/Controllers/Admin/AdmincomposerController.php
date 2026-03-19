<?php
/*
* =======================================================================
* FILE NAME:        AdmincomposerController.php
* DATE CREATED:  	05-01-2019
* FOR TABLE:  		admin_composer
* AUTHOR:			DH Fusion Ltd.
* CONTACT:			http://dhfusion.co.uk <info@dhfusion.co.uk>
* =======================================================================
*/
namespace App\Http\Controllers\Admin;
use App\Helpers\AppHelper;
use App\Http\Controllers\Traits\Calculations;
use App\Http\Controllers\Traits\Uploader;

use App\Mail\Notices;
use App\Models\Admincomposer;

use App\Models\Customers;
use App\Models\Messagesstatus;
use App\Models\Newsletter;
use App\Models\Settings;
use App\Models\System\Upload;
use App\Http\Controllers\Controller;
use App\User;
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

class AdmincomposerController extends Controller
{
    use Uploader;

    /**
     * AdmincomposerController constructor.
     */
    public function __construct() {
        $this->middleware(['auth', 'verifier']);
    }
    /**
     * This method display admincomposer view for datatable
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('admin.admincomposer.index');
    }
    /**
     * Load admincomposer data for view table
     * @return mixed
     */
    public function getdata()
    {
        $admincomposer = Admincomposer::join('users','users.id','=','admin_composer.message_by')
            ->select('admin_composer.*','users.username');
        return Datatables::of($admincomposer)
            ->addColumn('checkbox', function($admincomposer){
                return '<input type="checkbox" name="checkbox[]" id="box-'. $admincomposer->id .'" class="check-style filled-in blue"  onclick="toggleBtn()" value="'.$admincomposer->id.'"> 
                <label for="box-'. $admincomposer->id .'" class="checkinner"></label>';
            })
            ->editColumn('document', function($admincomposer){
                if($admincomposer->document) {
                    return '<a href="'.asset('public/uploads/'.$admincomposer->document.'').'" target="_blank" class="btn btn-default btn-xs"><i class="fa fa-file-o"></i></a>';
                }else{
                    return '';
                }
            })
            ->editColumn('sent', function($admincomposer){
                $pending = Customers::join('messages_status','messages_status.customer_id','=','customers.contact_id')
                    ->where('customers.customer_email','!=','')
                    ->where('sent_status',0)
                    ->where('message_id',$admincomposer->id)
                    ->count();
                $send = Customers::join('messages_status','messages_status.customer_id','=','customers.contact_id')
                    ->where('customers.customer_email','!=','')
                    ->where('sent_status',1)
                    ->where('message_id',$admincomposer->id)
                    ->count();
                $limitMs = Settings::limit(1)->first()->send_limit;
                if($limitMs){
                    $limit = $limitMs;
                }else{
                    $limit = 50;
                }
                if(($pending/$limit)>0) {
                    return '<a href="#" class="btn btn-default btn-xs">'.$send.' of '.$pending.'</a>';
                }else{
                    return '<a href="#" class="btn btn-success btn-xs">All Sent</a>';
                }
            })

            ->addColumn('sendMs', function($admincomposer){
                //if($admincomposer->document) {
                return '<a href="'.route('admincomposer.send.preview',['id'=>$admincomposer->id]).'" class="btn btn-outline-success btn-xs">Start Sending</a>';
                //}else{
                //return '';
                //}
            })
            ->editColumn('created_at', function($admincomposer){
                if($admincomposer->created_at)
                    return Carbon::parse($admincomposer->created_at,config('timezone'))->format('d-m-Y');
            })
            ->addColumn('action', function($admincomposer){
                return '
           <div class="btn-group btn-group-xs" role="group" aria-label="actions"> 
           <a href="'.route('admincomposer.edit',['id'=>$admincomposer->id]).'"  class="btn btn-success btn-xs"><i class="fa fa-pencil"></i></a> 
           <a href="javascript:viod(0)" data-id="row-'. $admincomposer->id .'" onclick="deleteData(\''.url('admin/admincomposer/delete').'\','.$admincomposer->id.')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a> 
           </div>';
            })
            ->filterColumn('username', function($query, $keyword) {
                $query->whereRaw("users.username  like ?", ["%{$keyword}%"]);
            })
            ->rawColumns(['checkbox','action','document','sendMs','sent'])->make(true);
    }
    /**
     * This method display admincomposer view for datatable
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function insert(Request $request)
    {
        return view('admin.admincomposer.create' );
    }

    public function store(Request $request){
        /* validate admincomposer data */
        $validator = Validator::make($request->all(),
            [
                'title' => 'required',
                'message' => 'required',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => true,'message'=>$validator->errors()->all()]);
        }
        else {
            /* get post data */
            $data = array(
                'message_type' => $request->input('message_type'),
                'to' => $request->input('to'),
                'user_email' => $request->input('user_email'),
                'from' => $request->input('from'),
                'title' => $request->input('title'),
                'message' => $request->input('message'),
                'created_at' => $this->dateTime(),
                'updated_at' => $this->dateTime(),
                'message_by' => $this->memberId(),

            );
            /* insert post data */
            $data= Admincomposer::create($data);

            /* Logic upload */
            if ($request->hasFile('document')) {
                /* you can customize the validation to meet your need. Just replace $this->imageRules() with yours e.g [mines:jpg,png...] */
                $valFile = Validator::make($request->all(), [$this->imageRules()]);
                if ($valFile->fails()) {
                    return response()->json(['error' => true,'message'=>$valFile->errors()->all()]);
                }else{
                    $filekey = $request->file('document');
                    /* upload dir default is /uploads. You can change it e.g $this->singleupload($filekey,'resize','uploads/folder') */
                    $filename=$this->singleupload($filekey,'mixed'); /* use 'mixed' or 'resize' */
                    /* update DB table*/
                    $data->update(['document' => $filename]);
                }
            }
            $customers = Customers::where('customer_email','!=','')->get();
            foreach ($customers as $customer) {
                DB::transaction(function() use($customer, $data) {
                    Messagesstatus::create([
                        'message_id' => $data->id,
                        'customer_id' => $customer->contact_id,
                        'sent_at' => $this->dateTime(),
                    ]);
                });
            }
            /* return json message */
            return response()->json(['success' => true,'message' => trans('app.add.success')]);
        }
    }

    public function sendPreview($id){
        $pending = Customers::join('messages_status','messages_status.customer_id','=','customers.contact_id')
            ->where('customers.customer_email','!=','')
            ->where('sent_status',0)
            ->where('message_id',$id)
            ->count();
        $limitMs = Settings::limit(1)->first()->send_limit;
        if($limitMs){
            $limit = $limitMs;
        }else{
            $limit = 50;
        }
        return view('admin.admincomposer.send-preview', compact('pending','limit','id'));
    }

    public function sendEmails($id){

        $limitMs = Settings::limit(1)->first()->send_limit;
        if($limitMs){
            $limit = $limitMs;
        }else{
            $limit = 50;
        }

        $msg = Admincomposer::findOrFail($id);
        $title = $msg->title;
        $message = $msg->message;
        $filekey = $msg->document;

        //$customers = Customers::where('customer_email','!=','')->get();
        $customers =  Customers::join('messages_status','messages_status.customer_id','=','customers.contact_id')
            ->where('customers.customer_email','!=','')
            ->where('sent_status',0)
            ->orderBy('messages_status.id')
            ->limit($limit)
            ->get();
        //print_r($customers)
        foreach ($customers as $customer) {

            $customer_email = trim($customer->customer_email);
            $email_status = $customer->sent_status;

            if (strpos($customer_email, ',') === false) {
                if (filter_var($customer_email, FILTER_VALIDATE_EMAIL) and $email_status==0) {
                    Mail::to($customer->customer_email)->send(new Notices($title, $message, $filekey));
                    Messagesstatus::where('customer_id', $customer->contact_id)->where('message_id', $msg->id)
                        ->update(['sent_status'=>1]);
                } else {
                    $emailSent[] = '<span class="text-danger">Invalid email address or a duplicate ' . $customer_email . '</span>';
                }
            } else {
                $custEmails = explode(',', $customer_email);
                foreach ($custEmails as $customerEmail) {
                    if (filter_var($customerEmail, FILTER_VALIDATE_EMAIL) and $email_status==0) {
                        Mail::to($customerEmail)->send(new Notices($title, $message, $filekey));
                        Messagesstatus::where('customer_id', $customer->contact_id)->where('message_id', $msg->id)
                            ->update(['sent_status'=>1]);
                    } else {
                        $emailSent[] = '<span class="text-danger">Invalid email address or a duplicate ' . $customerEmail . '</span>';
                    }
                }
            }
            $emailSent[] = '<span class="text-success">Mail sent to ' . $customer->customer_email . '</span>';

        }

        $pending = Customers::join('messages_status','messages_status.customer_id','=','customers.contact_id')
            ->where('customers.customer_email','!=','')
            ->where('sent_status',0)
            ->where('message_id',$id)
            ->count();
        $limitMs = Settings::limit(1)->first()->send_limit;
        if($limitMs){
            $limit = $limitMs;
        }else{
            $limit = 50;
        }

        return view('admin.admincomposer.sent', compact('emailSent','pending','limit','id'));
        //return response()->json(['success' => true,'message' => 'Message sent successfully']);
    }


    /*MAIL DIRECT FROM FORM*/
    /*public function sendEmails(Request $request){
        $validator = Validator::make($request->all(),
            [
                'title' => 'required',
                'message' => 'required',
            ]
        );
        $title = $request->input('title');
        $message = $request->input('message');

        if ($validator->fails()) {
            return response()->json(['error' => true,'message'=>$validator->errors()->all()]);
        }
        else {
            if ($request->hasFile('document')) {
                $valFile = Validator::make($request->all(), [$this->imageRules()]);
                if ($valFile->fails()) {
                    return response()->json(['error' => true,'message'=>$valFile->errors()->all()]);
                }else{
                    $filekey = $request->file('document');
                }
            }else{
                $filekey = '';
            }
            $customers = Customers::where('customer_email','!=','')->get();
            foreach ($customers as $customer) {
                Mail::to($customer->customer_email)->send(new Notices($title, $message, $filekey));
            }
            return response()->json(['success' => true,'message' => 'Message sent successfully']);
        }
    }*/


    /**
     * Select admincomposer edit
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id){

        $admincomposer = Admincomposer::findOrFail($id);
        /* pass admincomposer data to view and load list view */
        return view('admin.admincomposer.edit', compact('admincomposer' ));
    }

    /**
     * This method process admincomposer edit form
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request){
        /* validate admincomposer data */
        $validator = Validator::make($request->all(),
            [
                'title' => 'required',
                'message' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => true,'message'=>$validator->errors()->all()]);
        }
        else {
            $admincomposer = Admincomposer::findOrFail($id);
            $admincomposer->title = $request->input('title');
            $admincomposer->message = $request->input('message');
            $admincomposer->save();
            return response()->json(['success' => true,'message' => trans('app.update.success')]);
        }
    }

    /**
     * This method delete record from database and also delete associate file, if available.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id){
        if ( $request->ajax() ) {
            $admincomposer = Admincomposer::findOrFail($id);
            if($admincomposer->document) {
                $this->deleteFile('uploads/'.$admincomposer->document);
                $admincomposer->document='';
                $admincomposer->save();
            }
            Admincomposer::findOrFail($id)->delete();
            Messagesstatus::where('message_id',$id)->delete();
            return response()->json(['success' => true, 'message' => trans('app.delete.success')]);
        }
        return response()->json(['error' => true, 'message' => trans('app.delete.error')]);
    }

    /**
     * This method delete file associated with a record.
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyFile(Request $request, $id) {
        if ( $request->ajax() ) {
            $admincomposer = Admincomposer::findOrFail($id);
            $this->deleteFile('uploads/'.$admincomposer->document);
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
                Admincomposer::where('id', $id)->delete();
                Messagesstatus::where('message_id',$id)->delete();
            }
            return response()->json(['success' => 'delete', 'message' => trans('app.delete.success')]);
        }
        return response()->json(['error' => true, 'message' => trans('app.delete.error')]);
    }

}
