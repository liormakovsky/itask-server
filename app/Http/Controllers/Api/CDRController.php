<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use App\Models\Cdr;
use GuzzleHttp\Client;


class CDRController extends BaseController
{
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
        
                  $insertData = [
                     "customer_id"=>$importData[0],
                     "date_time"=>$importData[1],
                     "num_of_calls"=>$importData[2],
                     "did"=>$importData[3],
                     "ip_address"=>$importData[4]
                  ];
                  $cdr = Cdr::create($insertData);

                   if(!$cdr){
                       return $this->sendError('Failed to Create cdrs', [],500);    
                   }
                }
        
                return $this->sendResponse([], 'file imported with success');
        } 
        
        public function getCdrs(Request $request)
        {
            $ip_address = getUserIpAddr();
            $access_key = 'API_ACCESS_KEY';
            
            try {
                $client = new Client([
                    "base_uri" => "http://api.ipapi.com",
                ]);
                  
                $response = $client->request("GET", "/$ip_address", [
                    "query" => [
                        "access_key" => $access_key,
                    ]
                ]);
                  
                //get status code using $response->getStatusCode();
              
                $body = $response->getBody();
                $arr_result = json_decode($body, true);
                print_r($arr_result);
            } catch(Exception $e) {
                echo $e->getMessage();
            }
                      $key = 'b9c9e0c9e04642f5a66b2278c4cb1e25'
                    }
}

    