<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Models\Cart;
use App\Models\CartProduct;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class CartController extends Controller
{
    //
    protected $user_id;
    public function __construct(Request $request)
    {
        # pass bearer token in Authorizations key...
        // $token = $request->header('Authorizations');
        if (! $request->hasHeader('Authorizations')) {
            // die('Hi');
            response()->json(["message"=>"Unauthorized"],401)->send();
            exit();
            // return response()->json(["message"=>"Token required"]);
        } else {
            $bearer_token = $request->header('Authorizations');
            $token = str_replace("Bearer ","",$bearer_token);
            // dd($token);
            try {
                $this->user_id = Crypt::decrypt($token);
                $staff = User::find($this->user_id);
                if(empty($staff->status)) {
                    response()->json(["message"=>"Inactive User"],401)->send();
                    exit();
                }
                
            } catch (DecryptException $e) {
                response()->json(["message"=>"Mismatched token"],400)->send();
            }
        }
    }
    
    public function save(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(),[
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric'
        ]);

        if(!$validator->fails()){

            $params = $request->except('_token');
            $user_id = $this->user_id;            
            $customer_id = $params['customer_id'];
            $product_id = $params['product_id'];
            $quantity = $params['quantity'];

            

            $checkExistCart = Cart::where('customer_id',$customer_id)->where('user_id',$user_id)->first();

            if(!empty($checkExistCart)){

                $cart_id = $checkExistCart->id;

            } else {
                $cartData = array('user_id'=>$user_id,'customer_id'=>$customer_id);
                $cart_id = Cart::insertGetId($cartData);
                $existProduct = CartProduct::where('cart_id',$cart_id)->where('product_id',$product_id)->first();
                if(!empty($existProduct)){
                    CartProduct::where('id',$existProduct->id)->update(['product_id'=>$product_id,'quantity'=>$quantity]);
                }else{
                    CartProduct::insert(['cart_id'=>$cart_id,'product_id'=>$product_id,'quantity'=>$quantity]);                    
                }
                Cart::where('id',$cart_id)->update(['total_items'=>$quantity]);
            }

            $cart = Cart::select('total_amount','total_items')->find($cart_id);
            $items = CartProduct::select('product_id','quantity','product_price','product_total_price')->where('cart_id',$cart_id)->get();
            $cart->items = $items;
            
            return Response::json(['status' => true, 'message' => "Items Added To Cart", 'data' => array('cart'=>$cart) ],200);
        } else {
            
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }


    }

    public function remove(Request $request)
    {
        # remove cart item ...
        $validator = Validator::make($request->all(),[
            'cart_id' => 'required|exists:carts,id',
            'product_id' => 'required|exists:products,id'
        ]);
        $params = $request->except('_token');
        $cart_id = $params['cart_id'];
        $product_id = $params['product_id'];

        CartProduct::where('cart_id',$cart_id)->where('product_id',$product_id)->delete();
        $count_cart_products = CartProduct::where('cart_id',$cart_id)->count();
        if($count_cart_products == 0){
            Cart::where('id',$cart_id)->delete();
        }
        
        return Response::json(['status' => true, 'message' => "Item removed successfully", 'data' => array() ],200);

    }
}
