<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CallbackController extends Controller
{
    //
    public function __construct(Request $request)
    {
        # code...
        
        if (! $request->hasHeader('KGA-Order-Key')) {
            response()->json(["status"=>false,"message"=>"Unauthorized"],401)->send();
            exit();
        } else {
            $kga_order_key = $request->header('KGA-Order-Key');
            if($kga_order_key != 'kga.oneness@2023'){
                response()->json(["status"=>false,"message"=>"Mismatched Key"],400)->send();
                exit();
            }
                
            
        }
    }

    public function kga_sales_order(Request $request)
    {
        # code...
        $file = 'order.txt';
        // $json_body = json_encode($request->all());
        $myfile = fopen($file, "w") or die("Unable to open file!");
        
        $txt = json_encode($request->all());
        fwrite($myfile, $txt);
        fclose($myfile);

        // $myData = json_decode($txt);
        // $fname = $myData->fname;
        // echo $fname;
        return response()->json(["status"=>true,"message"=>"KGA Sales Order Fetched" ],200);
        
    }
   
}
