<?php

namespace App\Http\Controllers\Traits;
use App\Models\Sales;
use App\Models\System\Upload;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
//use File;
trait  Uploader
{
    public function imageRules(){
        return 'image|mimes:jpeg,bmp,png,gif,jpg|max:5000';
    }
    public function mixedRules(){
        return 'mimes:jpeg,bmp,png,pdf,doc,docx,mp4,mkv,mpeg,avi,csv,xls,xlsx,zip,rar,txt|max:5000';
    }

    public function upload($fname,$relatedId)
    {
        $time = Carbon::now(config('timezone'));
        $image = $fname;
        $extension = $image->getClientOriginalExtension();
        $directory = date_format($time, 'Y') . '/' . date_format($time, 'm');
        $filename = Str::random(5) . date_format($time, 'd') . rand(1, 9) . date_format($time, 'h') . "." . $extension;
        $upload_success = $image->storeAs($directory, $filename, 'public');
        // If the upload is successful, return the name of directory/filename of the upload.
        if ($upload_success) {
            return response()->json($upload_success, 200);
        } // Else, return error 400
        else {
            return response()->json('error', 400);
        }
    }

    /**
     * Multiple file or image upload
     * @param $fname
     * @param $relatedId
     * @param $table
     * @param $type
     * @param string $publicDir
     */
    public function multipleupload($fname, $relatedId, $table, $type, $publicDir='uploads')
    {
        foreach($fname as $file) {
            $time = Carbon::now(config('timezone'));
            $extension = $file->getClientOriginalExtension();
            $directory = date_format($time, 'Y') . '/' . date_format($time, 'm');
            $filename = Str::random(5) . date_format($time, 'd') . rand(1, 9) . date_format($time, 'h') . "." . $extension;
            if($type=='resize') {
                if(!is_dir(public_path($publicDir. '/' .$directory))) {
                    Storage::disk('public')->MakeDirectory($publicDir. '/' . $directory);
                }
                $upload_success = Image::make($file)->fit(500, 400)->save(public_path($publicDir . '/' . $directory . '/' . $filename));
            }else{
                $upload_success = $file->storeAs($publicDir.'/'.$directory, $filename, 'public');
            }
            if ($upload_success) {
                Upload::create(
                    ['relatedId' => $relatedId, 'filename' => $directory . '/' . $filename,'tablekey' => $table]
                );
            }
        }
    }

    /**
     * Upload single file or image or image resize
     * @param $fname
     * @param $type
     * @param string $publicDir
     * @return string
     */
    public function singleupload($fname, $type, $publicDir='uploads')
    {
        $time = Carbon::now(config('timezone'));
        $extension = $fname->getClientOriginalExtension();
        $directory = date_format($time, 'Y') . '/' . date_format($time, 'm');
        $filename = Str::random(5) . date_format($time, 'd') . rand(1, 9) . date_format($time, 'h') . "." . $extension;
        if($type=='resize') {
            if(!is_dir(public_path($publicDir. '/' .$directory))) {
                Storage::disk('public')->MakeDirectory($publicDir . '/' . $directory);
            }
            Image::make($fname)->fit(500, 400)->save(public_path($publicDir . '/' . $directory . '/' . $filename));
        }else{
            $fname->storeAs($publicDir.'/'.$directory, $filename, 'public');
        }
        return $directory . '/' . $filename;
    }

    /**
     * Delete file if exist
     * @param string $publicDir
     */
    public function deleteFile($publicDir='uploads/')
    {
        if(file_exists(public_path($publicDir))){
            unlink(public_path($publicDir));
        }
    }

    /**
     * Delete file from file and from database
     * @param $id
     * @param string $publicDir
     */
    public function deleteFileWith($id, $publicDir='uploads')
    {
        $upload = Upload::findOrFail($id);
        if($upload->filename) {
            if (file_exists(public_path($publicDir . '/' . $upload->filename))) {
                unlink(public_path($publicDir . '/' . $upload->filename));
            }
        }
        Upload::findOrFail($id)->delete();
    }

    /*User Info*/
    public function memberId($id=null){
        if($id){
            $userid=$id;
        }else{
            $userid=Auth::user()->id;
        }
        return $userid;
    }

    public function memberInfo($id=null){
        if($id){
            $userid=$id;
        }else{
            $userid=Auth::user()->id;
        }
        return User::where('id','=',$userid)->first();
    }

    /*General Setting*/
    public function adminSettings($id=null){
        if($id){
            $userid=$id;
        }else{
            $userid=Auth::user()->id;
        }
        return DB::table('user_settings')->where('user_id','=',$userid)->limit(1)->get()->first();
    }

    public function dateTime(){
        return Carbon::now(config('timezone'))->toDateTimeString();
    }


    function checkInvoiceNumber($invoice, $timestamp){
        if(in_array($invoice, $this->valid_invoices)){
            return false;
        }
        if(Sales::where('invoice_number',$invoice)->where('upload_ts','!=',$timestamp)->count() >= 1){
            return true;
        }else{
            array_push($this->valid_invoices, $invoice);
            return false;
        }

    }

    public function upload_exist_checker($uploadcode) {
        if(Sales::where('uploadcode',$uploadcode)->count() >= 1){
            return true;
        }else{
            return false;
        }
    }

    public function ImportCSV(){
        if($_POST){
            $fileName=$_FILES['sales_file']['name'];
            $fileSize=$_FILES['sales_file']['size'];
            $ext = pathinfo(strtolower($fileName), PATHINFO_EXTENSION);
            $allowed = ['csv','xls','xlsx'];
            if(!in_array($ext,$allowed)){
                return 3;
            }
            /*elseif($fileSize > 5242880){
                return 4;
            }*/
            $csv_folder=public_path('uploads').'/';
            $file = $csv_folder.basename($fileName);
            $uniqid = substr($fileName,0,-4);
            $timestamp = time();
            if ($this->upload_exist_checker($uniqid) === true) {
                //echo'<h3 class="alert alert-danger">The file you trying to upload Already exist!</h3>';
                return 1;
            } else {
                //echo 'in else 156 <Br>';
                if (move_uploaded_file($_FILES['sales_file']['tmp_name'], $file)) {
                    $csvfile =$csv_folder.$fileName;
                    if(!file_exists($csvfile)) {
                        die("File not found. Make sure you specified the correct path.");
                    }
                    $pdo = DB::connection()->getPdo();
                    $pdo->beginTransaction();
                    $uploadCsvFile = fopen($csvfile, 'r');
                    $i = 0;
                    $error_records = array();
                    $successfull_records = array();
                    $this->valid_invoices = array();
                    $lineNumber = 1;
                    while (($raw_data = fgetcsv($uploadCsvFile, 1024, ",", '"')) !== FALSE) {
                        $csv_array = preg_replace('/[^A-Za-z0-9\.\,\- ]/', '',$raw_data);
                        if($csv_array[0] == '' || $csv_array[1] == ''||$csv_array[2] == ''){
                            if(!array_filter($csv_array)) {
                                //skipping blank line
                                continue;
                            }
                            array_push($error_records, 'Mandatory values (invoice, invoice date or customer name) missing on line number <strong>'.$lineNumber.'</strong>.');
                            $lineNumber++;
                            continue;
                        }
                        $invoice_invalid = $this->checkInvoiceNumber($csv_array[0], $timestamp );
                        if(count($csv_array) === 40 && $invoice_invalid === false) {
                            try{

                                $query = "INSERT INTO sales(invoice_number, invoice_date, customer_account, 
					   customer_name, address1, address2, town, country, postcode, spacer1, 
					   customer_account2, numb1, items, weight, invoice_total, numb2, spacer2, 
					   job_number, job_date, sending_deport, delivery_deport, destination, 
					   town2, postcode2, service_type, items2, volume_weight, numb3, 
					   increased_liability_cover, sub_total, spacer3, numb4, sender_reference, 
					   numb5, percentage_fuel_surcharge,percentage_resourcing_surcharge, spacer4, senders_postcode, vat_amount, 
					   vat_percent, uploadcode, upload_ts) 
						values( :field1,:field2,:field3,:field4,:field5,:field6,:field7,:field8,
								:field9,:field10,:field11,:field12,:field13,:field14,:field15,:field16,
								:field17,:field18,:field19,:field20,:field21,:field22,:field23,:field24,
								:field25,:field26,:field27,:field28,:field29,:field30,:field31,:field32,
								:field33,:field34,:field35,:field36,:field37,:field38,:field39,:field40,
								:field41)";
                                $stmt = $pdo->prepare($query);
                                //echo '<br>'.$query.'<br>';
                                $stmt->execute(array(':field1' => $csv_array[0],
                                    ':field2' => $csv_array[1],':field3' => $csv_array[2],
                                    ':field4' => $csv_array[3],':field5' => $csv_array[4],
                                    ':field6' => $csv_array[5],':field7' => $csv_array[6],
                                    ':field8' => $csv_array[7],':field9' => $csv_array[8],
                                    ':field10' => $csv_array[9],':field11' => $csv_array[10],
                                    ':field12' => $csv_array[11],':field13' => $csv_array[12],
                                    ':field14' => $csv_array[13],':field15' => $csv_array[14],
                                    ':field16' => $csv_array[15],':field17' => $csv_array[16],
                                    ':field18' => $csv_array[17],':field19' => $csv_array[18],
                                    ':field20' => $csv_array[19],':field21' => $csv_array[20],
                                    ':field22' => $csv_array[21],':field23' => $csv_array[22],
                                    ':field24' => $csv_array[23],':field25' => $csv_array[24],
                                    ':field26' => $csv_array[25],':field27' => $csv_array[26],
                                    ':field28' => $csv_array[27],':field29' => $csv_array[28],
                                    ':field30' => $csv_array[29],':field31' => $csv_array[30],
                                    ':field32' => $csv_array[31],':field33' => $csv_array[32],
                                    ':field34' => $csv_array[33],':field35' => $csv_array[34],
                                    ':field36' => $csv_array[35],':field37' => $csv_array[36],
                                    ':field38' => $csv_array[37],':field39' => $csv_array[38],':field40' => $csv_array[39],
                                    ':field41' => $uniqid, ':field42' => $timestamp));
                                array_push($successfull_records,$csv_array[0]);
                            } catch(\Exception $e){
                                array_push($error_records, 'SQL Error on line number <strong>' .$lineNumber . '</strong>, Invoice number <strong>' . $csv_array[0].'</strong> Error Message: '. $e->getMessage());
                                $lineNumber++;
                                continue;
                            }
                            $i++;
                        } else {
                            //echo 'invoice found :'.$csv_array[0].'<br>';
                            if(count($csv_array) != 40){
                                return 'invalid';
                                //array_push($error_records, 'Invalid line format, Line number <strong>' .$lineNumber . '</strong>, Invoice number <strong>' . $csv_array[0].'</strong>');
                            } else {
                                return 'exist';
                                //array_push($error_records, 'Invoice already imported in database Invoice number <strong>' . $csv_array[0].'</strong>');
                            }
                        }
                        $lineNumber++;
                    } //end while
                    fclose($uploadCsvFile);
                    if(count($error_records) > 0){
                        //echo'<p class="bg-danger">There is an error within your csv file and no data has been imported. Please contact your system administrator on 01782 479371</p>';
                        return 2;
                        $pdo->rollBack();
                    } else {
                        if(file_exists($csvfile)){unlink ($csvfile);}
                        $pdo->commit();
                        //echo'<script>window.location.replace("'.$redirect_to.'");</script>';
                        return true;
                    }
                    unset($pdo);
                }
            }
        }
    }/// END DataInFileImportCSV


    public function timeExtension(){
        @set_time_limit(60 * 60); //60 minites
        ini_set('memory_limit', '-1');
    }
}



