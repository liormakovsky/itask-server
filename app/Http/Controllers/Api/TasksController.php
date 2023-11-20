<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use App\Models\Cdr;
use App\Models\Country;
use GuzzleHttp\Client;
use DB;


class Tasksontroller extends BaseController
{   
    /**
     * Upload csv file into DB
     */
    public function uploadFile(Request $request)
    {
        
        if (empty($request->files) || !$request->hasFile('file')) {
            return $this->sendError('Missing file', ['error'=>'Missing file'],400);
        }
          
        $file = $request->file('file');
        // File Details 
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $tempPath = $file->getRealPath();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();
        
        // Valid File Extensions
        $valid_extension = ["csv"];
        
        // Check file extension
        if(!in_array(strtolower($extension),$valid_extension)){
            return $this->sendError('Invalid File Extension', ['error'=>'Invalid File Extension'],400);
        }

        // File upload location
        $location = 'uploads';
        
        // Upload file
        $file->move($location,$filename);
        
        // Import CSV to Database
        $filepath = public_path($location."/".$filename);
        
        // Reading file
        $file = fopen($filepath,"r");
        
        $importData_arr = array();
        $i = 0;
        
        while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
            $num = count($filedata );
                   
            for ($c=0; $c < $num; $c++) {
                $importData_arr[$i][] = $filedata [$c];
            }
            $i++;
        }
            fclose($file);

        // Insert to MySQL database
        foreach($importData_arr as $importData){
            $contDestination = NULL;
            if(!empty($importData[3])){
                $contDestination = Country::where('prefix', 'like', substr($importData[3], 0, 1).'__%')->pluck('continent');
                if(!empty($contDestination)){
                    $contDestination = $contDestination[0];
                }
            }
                    
           $insertData = [
                "customer_id"=>$importData[0],
                "date_time"=>$importData[1],
                "duration"=>$importData[2],
                "did"=>$importData[3],
                "ip_address"=>$importData[4],
                "cont_source"=>$this->getContinentCodeByIp($importData[4]),
                "cont_destination"=>$contDestination
            ];
            $cdr = Cdr::create($insertData);

            if(!$cdr){
                return $this->sendError('Failed to Create cdrs', [],500);    
            }
        }
        
        return $this->sendResponse([], 'file imported with success');
    } 
      

    /**
     * get Tasks
     */
    public function getTasks(Request $request){
            // $totals = DB::table('cdr')
            // ->selectRaw(
            //     'customer_id,
            //     count(*) as total_calls, 
            //     SUM(CASE WHEN cont_source = cont_destination THEN 1 ELSE 0 END) AS total_calls_same_cont,
            //     SUM(CASE WHEN cont_source = cont_destination THEN duration ELSE 0 END) AS total_duration_same_cont,
            //     SUM(duration) as total_duration')
            // ->groupBy('customer_id')
            // ->get();
            return $this->sendResponse([], '');
    }
        
                     
}

    