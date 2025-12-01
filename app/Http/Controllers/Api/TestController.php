<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class TestController extends Controller
{
    //

    public function index(Request $request)
    {
        # test...
        // die('Hi');
        $data = (object) array();
        return Response::json(['status' => true,'message' => "All data",'data' => $data], 200);
    }

    public function success(Request $request)
    {
        # success...
        $data = (object) array();
        return Response::json(['status' => true,'message' => "All data",'data' => $data], 200);
    }

    public function error(Request $request)
    {
        # success...
        $data = (object) array();
        return Response::json(['status' => false,'message' => "Oops!! Something went wrong",'data' => $data], 200);
    }

    public function save(Request $request)
    {
        # form-data post...
        $validator = Validator::make($request->all(),[
            'title' => 'required|min:5|max:50',
            'description' => 'required',
            'image' => 'required|mimes:png,jpg'
        ],[
            'title' => 'Please add title',
            'description' => 'Please add description',
            'imgae.required' => 'Please upload an image'
        ]);

        if(!$validator->fails()){
            $params = $request->except('_token');
            $image = $params['image'];
            $imageFileName = $image->getClientOriginalName();
            unset($params['image']);
            $params['imageFileName'] = $imageFileName;
            return Response::json(['status' => true, 'message' => "Form data submitted successfully", 'data' => array('params'=>$params) ],200);
        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }

    }

    public function create_token_by_userid(Request $request)
    {
        # Generate Crypt Toekn Using User Id...
        $id = !empty($request->id)?$request->id:'';

        if(!empty($id)){
            $token = Crypt::encrypt($id);
            return Response::json(['status' => true, 'message' => "Test Token User for id:- ".$id." ", 'data' => array('token'=>$token) ],200);
        } else {
            return Response::json(['status' => false, 'message' => "Please send id as user parameter" , 'data' => array(  ) ],400);
        }

    }
}
