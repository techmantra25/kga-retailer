<?php

namespace App\Http\Controllers\Api\ServicePartner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;
use App\Models\ServicePartner;
use App\Models\Product;


class ProductController extends Controller
{
    private $service_partner_id;
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
                $this->service_partner_id = Crypt::decrypt($token);
                $staff = ServicePartner::find($this->service_partner_id);           
            } catch (DecryptException $e) {
                response()->json(["status"=>false,"message"=>"Mismatched token"],400)->send();
                exit();
            }
        }
    }

    public function list(Request $request)
    {
        # SP or FG List...
        $validator = Validator::make($request->all(),[
            'type' => 'required|in:fg,sp',
            'search' => 'nullable',
            'spare_type' => 'nullable|in:general,motor'
        ]);

        if(!$validator->fails()){
            $type = $request->type;
            $search = !empty($request->search)?$request->search:'';
            $spare_type = !empty($request->spare_type)?$request->spare_type:'';
            $products = Product::select('id','title','type','mop','spare_type')->where('type',$type);
            $countProducts = Product::where('type',$type);
            
            if(!empty($search)){
                $products = $products->where('title', 'LIKE', '%'.$search.'%');
                $countProducts = $countProducts->where('title', 'LIKE', '%'.$search.'%');
            }
            if($type == 'sp' && !empty($spare_type)){
                $products = $products->where('spare_type', $spare_type);
                $countProducts = $countProducts->where('spare_type', $spare_type);
            }
            
            $products = $products->orderBy('title')->get();
            $countProducts = $countProducts->count();

            return Response::json([
                'status' => true, 
                'message' => ucwords($type)." product list ",
                'data' => array(
                    'countProducts' => $countProducts,
                    'products' => $products
                )
            ],200);
            
        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }
    }

    public function details($id)
    {
        # Details...
        $product = Product::find($id);

        return Response::json([
            'status' => true, 
            'message' => 'Product Details',
            'data' => array(
                'product' => $product
            )
        ],200);
    }


}
