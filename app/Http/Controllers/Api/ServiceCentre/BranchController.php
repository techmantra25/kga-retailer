<?php

namespace App\Http\Controllers\Api\ServiceCentre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;
use App\Models\ServiceCentre;
use App\Models\Branch;

class BranchController extends Controller
{
    private $service_centre_id;
    public function __construct(Request $request)
    {
        # pass bearer token in Authorizations key...
        if (! $request->hasHeader('Authorizations')) {
            response()->json(["status"=>false,"message"=>"Unauthorized"],401)->send();
            exit();
        } else {
            $bearer_token = $request->header('Authorizations');
            $token = str_replace("Bearer ","",$bearer_token);            
            try {
                $this->service_centre_id = Crypt::decrypt($token);
                $staff = ServiceCentre::find($this->service_centre_id);           
            } catch (DecryptException $e) {
                response()->json(["status"=>false,"message"=>"Mismatched token"],400)->send();
                exit();
            }
        }
    }

    public function list(Request $request)
    {
        # code...

        $validator = Validator::make($request->all(),[
            'search' => 'nullable'
        ]);

        if(!$validator->fails()){
            $search = !empty($request->search)?$request->search:'';
            $branches = Branch::select('id','name');
            $countBranch = Branch::select('id','name');
            
            if(!empty($search)){
                $branches = $branches->where('name', 'LIKE', '%'.$search.'%');
                $countBranch = $countBranch->where('name', 'LIKE', '%'.$search.'%');
            }
                        
            $branches = $branches->orderBy('name')->get();
            $countBranch = $countBranch->count();

            return Response::json([
                'status' => true, 
                'message' => "Branch List ",
                'data' => array(
                    'countBranch' => $countBranch,
                    'branches' => $branches
                )
            ],200);
            
        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }


    }
}
