<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\ServicePartnerPincode;

class PincodeController extends Controller
{
    //

    public function available(Request $request)
    {
        # is available service partners on pincode ...

        $pincode = !empty($request->pincode)?$request->pincode:'';
        if(!empty($pincode)){
            $pincodes = ServicePartnerPincode::where('number',$pincode)->first();

            if(!empty($pincodes)){
                return Response::json(['status' => true,'message' => "Service Parner Available",'data' => array( )], 200);
            } else {
                return Response::json(['status' => false,'message' => "Sorry! No Service Partner Available On This Pincode",'data' => array( )], 200);
            }
        } else {
            return Response::json(['status' => false,'message' => "Please add pincode",'data' => array( )], 200);
        }
        

        // dd($pincodes);
        

    }
}
