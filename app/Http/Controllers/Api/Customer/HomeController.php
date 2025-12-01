<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class HomeController extends Controller
{
    //
    public function __construct(Request $request)
    {
        # code...
    }

    public function index(Request $request)
    {
        # code...
        // die("Customer - Home");
        return Response::json(['status'=>true,'message'=>"Customer - Home",'data'=> (object)[] ],200);
    }
}
