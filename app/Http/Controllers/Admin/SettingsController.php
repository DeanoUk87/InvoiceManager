<?php
/*
* =======================================================================
* FILE NAME:        SettingsController.php
* DATE CREATED:  	17-01-2019
* FOR TABLE:  		settings
* AUTHOR:			DH Fusion Ltd.
* CONTACT:			http://dhfusion.co.uk <info@dhfusion.co.uk>
* =======================================================================
*/
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Traits\Uploader;

use App\Models\Settings;

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
	
class SettingsController extends Controller
{
    use Uploader;
    
	/**
    * SettingsController constructor.
    */
    public function __construct() {
        $this->middleware(['auth', 'verifier']);
    }     

    public function index(Request $request)
    {
        if(Auth::user()->hasAnyRole(['admin','admin2'])) {
            $settings = Settings::limit(1)->first();
            return view('admin.settings.edit', compact('settings'));
        }
    }

    /**
     * This method process settings edit form
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request){
       /* validate settings data */
        if(Auth::user()->hasAnyRole(['admin','admin2'])) {
            $validator = Validator::make($request->all(),
                [
                    'company_name' => 'required',
                ]
            );

            if ($validator->fails()) {
                return response()->json(['error' => true, 'message' => $validator->errors()->all()]);
            } else {
                $settings = Settings::findOrFail($id);
                if(Auth::user()->hasRole('admin')) {
                    $settings->company_name = $request->input('company_name');
                    $settings->send_limit = $request->input('send_limit');
                    $settings->company_address1 = $request->input('company_address1');
                    $settings->company_address2 = $request->input('company_address2');
                    $settings->state = $request->input('state');
                    $settings->city = $request->input('city');
                    $settings->postcode = $request->input('postcode');
                    $settings->country = $request->input('country');
                    $settings->phone = $request->input('phone');
                    $settings->fax = $request->input('fax');
                    $settings->cemail = $request->input('cemail');
                    $settings->website = $request->input('website');
                    $settings->primary_contact = $request->input('primary_contact');
                    $settings->base_currency = $request->input('base_currency');
                    $settings->vat_number = $request->input('vat_number');
                    $settings->invoice_due_date = $request->input('invoice_due_date');
                    $settings->invoice_due_payment_by = $request->input('invoice_due_payment_by');
                    $settings->message_title = $request->input('message_title');
                }
                $settings->default_message = $request->input('default_message');
                $settings->default_message2 = $request->input('default_message2');

                /* Logic upload */
                if ($request->hasFile('logo')) {
                    /* you can customize the validation to meet your need. Just replace $this->imageRules() with yours e.g [mines:jpg,png...] */
                    $valFile = Validator::make($request->all(), [$this->imageRules()]);
                    if ($valFile->fails()) {
                        return response()->json(['error' => true, 'message' => $valFile->errors()->all()]);

                    } else {
                        $filekey = $request->file('logo');
                        /* upload dir default is /uploads. You can change it e.g $this->singleupload($filekey,'resize','uploads/folder') */
                        $filename = $this->singleupload($filekey, 'mixed'); /* use 'mixed' or 'resize' */
                        /* update DB table*/
                        $settings->logo = $filename;
                    }
                }
                $settings->save();
                return response()->json(['success' => true, 'message' => trans('app.update.success')
                ]);
            }
        }
    }

    /**
     * This method delete file associated with a record.
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyFile(Request $request, $id) {
        if ( $request->ajax() ) {
            $settings = Settings::findOrFail($id);
            $this->deleteFile('uploads/'.$settings->logo);
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


}
	