<?php

namespace App\Http\Controllers\Api\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\Dealer;
use App\Models\DealerEmployee;

class AuthController extends Controller
{
    
    public function login(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(),[
            'phone' => 'required|exists:dealer_employee,phone',
            'password' => 'required',
            'mac_address' => 'required'
        ]);

        if (!$validator->fails()){
            $params = $request->except('_token');
            $phone = $params['phone'];
            $password = $params['password'];
            $checkUser = DealerEmployee::where('phone',$phone)->first();
            if(!empty($checkUser)){
                $checkPassword = ($password == $checkUser->password);
                if($checkPassword){
                    if(!empty($checkUser->mac_address)){
                        return Response::json(['status' => false, 'message' => "Already logged in a device. Please logout first",'type' => $checkUser->from_where == 1 ? 'dealer' : 'employee' ],200);
                    }
                    DealerEmployee::where('id',$checkUser->id)->update([
                        'mac_address' => $params['mac_address']
                    ]);
                    $token = Crypt::encrypt($checkUser->id);
                    return Response::json(['status' => true, 'message' => "Logged in successfully", 'type' => $checkUser->from_where == 1 ? 'dealer' : 'employee','data' => array('token'=>$token,'user'=>$checkUser) ],200);
                }else{
                    return Response::json(['status' => false, 'message' => "Password mismatched" ],200);
                }
            }else{
                return Response::json(['status' => false, 'message' => "No user found" ],200);
            }
        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }
    }


    public function logout(Request $request)
    {
        # logout...

        if (! $request->hasHeader('Authorizations')) {
            response()->json(["status"=>false,"message"=>"Unauthorized"],400)->send();
            exit();
        } else {
            $bearer_token = $request->header('Authorizations');
            $token = str_replace("Bearer ","",$bearer_token);            
            try {
                $user_id = Crypt::decrypt($token);  
                $user = DealerEmployee::find($user_id);
                
                DealerEmployee::where('id',$user_id)->update([
                    'mac_address' => null
                ]);
                return Response::json(['status'=>true,'message'=>"Logged out successfully", 'data' => (object) array('user' => $user)],200);         
            } catch (DecryptException $e) {
                response()->json(["status"=>false,"message"=>"Mismatched token"],400)->send();
            }
        }

    }
}
