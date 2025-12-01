<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\SpareGoods;
use App\Models\ProductAmc;
use App\Models\CustomerAmcRequest;
use App\Models\GoodsWarranty;
use App\Models\ProductWarranty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Maatwebsite\Excel\Facades\Excel;
use File;

class ProductController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {            
            if(Auth::user()->id == 8){                
                abort(404);                
            }
            return $next($request);
        });
    }
    /**
     * Display a listing of the product.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = !empty($request->search)?$request->search:'';
        $status = !empty($request->status)?$request->status:'';
        $type = !empty($request->type)?$request->type:'';
        $paginate = !empty($request->paginate)?$request->paginate:25;
        $cat_id = !empty($request->cat_id)?$request->cat_id:'';
        $subcat_id = !empty($request->subcat_id)?$request->subcat_id:'';
        $total = Product::count();
        $page = $request->page;
        if(!is_numeric($page)){
            $page = 1;
        }
        if(!is_numeric($paginate)){
            $paginate = 25;
        }
        
        $totalActive = Product::where('status', 1)->count();
        $totlInactive = Product::where('status', 0)->count();
        $data = Product::select('*');
        $totalResult = Product::select('id');

        $category = Category::select('id','name')->whereNull('parent_id');
        $sub_category = Category::select('id','name');


        if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('title', 'LIKE','%'.$search.'%')
                ->orWhere('public_name','LIKE','%'.$search.'%');
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->where('title', 'LIKE','%'.$search.'%')
                ->orWhere('public_name','LIKE','%'.$search.'%');
            });
        }
        if(!empty($type)){
            $data = $data->where('type', $type);
            $totalResult = $totalResult->where('type', $type);

            
            $category = $category->where('product_type', $type);
            $sub_category = $sub_category->where('product_type', $type)->where('parent_id',$cat_id);
            
        }
        
        if(!empty($cat_id)){
            $data = $data->where('cat_id', $cat_id);
            $totalResult = $totalResult->where('cat_id', $cat_id);
        }
        if(!empty($subcat_id)){
            $data = $data->where('subcat_id', $subcat_id);
            $totalResult = $totalResult->where('subcat_id', $subcat_id);
        }
        if($status == 'active'){
            $data = $data->where('status', 1);
            $totalResult = $totalResult->where('status', 1);
        } else if ($status == 'inactive'){
            $data = $data->where('status', 0);
            $totalResult = $totalResult->where('status', 0);
        }
        $data = $data->orderBy('id','desc')->paginate($paginate);
        $totalResult = $totalResult->count();

        $category = $category->where('status', 1)->orderBy('name')->get();
        $sub_category = $sub_category->where('status', 1)->orderBy('name')->get();

        // dd($paginate);        
        $data = $data->appends([
            'search'=>$search,
            'type'=>$type,
            'status'=>$status,
            'page'=>$page,
            'cat_id'=>$cat_id,
            'subcat_id'=>$subcat_id,
            'paginate'=>$request->paginate
        ]);
        return view('product.list', compact('data','totalResult','total','totalActive','totlInactive','status','search','type','paginate','page','cat_id','subcat_id','category','sub_category'));
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $category = Category::where('status', 1)->whereNull('parent_id')->orderBy('name','asc')->get();
        
        return view('product.add', compact('category'));
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       //dd($request->all());
    try {
       $request->validate([
        'title' => 'required|max:100|unique:products,title',
        'public_name' => 'required|max:100|unique:products,title',
        'cat_id' => 'required|exists:categories,id',
        'subcat_id' => 'nullable|exists:categories,id',
        'service_level' => 'nullable|required_if:type,fg|in:customer,dealer',
        'type' => 'required|in:fg,sp',
        'warranty_status' => 'nullable',
        // 'warranty_period' => 'nullable|required_if:warranty_status,yes',
        'warranty_period' => 'nullable',
        'mop' => 'nullable',
        'set_of_pcs' => 'required_if:type,sp',
        'spare_type' => 'required_if:type,sp',
        // 'comprehensive_warranty' => 'required_if:goods_type,chimney',
        'comprehensive_warranty' => 'nullable',
        // 'comprehensive_warranty_free_services' => 'required_if:goods_type,chimney',
        'comprehensive_warranty_free_services' => 'nullable',
        // 'extra_warranty' => 'required_if:goods_type,chimney',
        'extra_warranty' => 'nullable',
        'installable_amount' => 'nullable|required_if:goods_type,chimney|numeric',
        // 'motor_warranty' => 'required_if:goods_type,chimney',
        'motor_warranty' => 'nullable',
        'profit_percentage' => 'required_if:type,sp',
        // 'supplier_warranty_period' => 'required_if:type,fg'
    ],[
        'cat_id.required' => 'The category field is required. ',
        'set_of_pcs.required_if' => 'Please add set of pieces for spares',
        'spare_type.required_if' => 'Please select spare type',
        'service_level.required_if' => 'Service level is required',
        // 'comprehensive_warranty.required_if' => 'Please add free service tenure',
        // 'comprehensive_warranty_free_services.required_if' => 'Please add no of free services',
        // 'extra_warranty.required_if' => 'Please add additional warranty tenure',
        'installable_amount.required_if' => 'Please add installation amount',
        'installable_amount.numeric' => 'Installation amount must be numeric',
        // 'motor_warranty.required_if' => 'Please add motor warranty tenure',
        'profit_percentage.required_if' => 'Please add profit percentage',
        // 'supplier_warranty_period.required_if' => 'Please add supplier warranty period'
    ]); 

        $params = $request->except('_token');
        // dd($params);
        $params['installable_amount'] = $request->get('installable_amount')?$request->get('installable_amount'):0;
        if($params['type'] == 'sp'){
            $params['service_level'] = null;            
        } else {
            $params['set_of_pcs'] = 1;
        }

        $params['is_test_product'] = isset($params['is_test_product'])?$params['is_test_product']:0;
        $params['is_installable'] = isset($params['is_installable'])?$params['is_installable']:0;
        $params['is_amc_applicable'] = isset($params['is_amc_applicable'])?$params['is_amc_applicable']:0;

        // if($params['type'] == 'fg' && $params['goods_type'] == 'general' && empty($params['warranty_status']) ){
        //     return redirect()->back()->withErrors(['warranty_status' => 'Please choose warranty status'])->withInput();
        // }

        
        $params['unique_id'] = genAutoIncreNo();
        unset($params['subcat_name']);
        $params['created_at'] = date('Y-m-d H:i:s');     
        
         //dd($params);
        $id = Product::insertGetId($params);
                
        if (!empty($id)) {
            Session::flash('message', 'Product created successfully');
            return redirect()->route('product.list');
        } else {
            return redirect()->route('product.add')->withInput($request->all());
        }
    } catch (\Exception $e) {
        // Log the error for debugging
        // dd($e->getMessage());

        // Redirect back with error message
        return redirect()->back()->withErrors(['error' => 'An unexpected error occurred. Please try again later.'])->withInput();
    }
    
    }
    

    /**
     * Display the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $data = Product::with('category','subcategory')->find($id);
            return view('product.detail', compact('data','id','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $data = Product::with('category','subcategory')->find($id);
            $category = Category::where('status', 1)->whereNull('parent_id')->where('product_type',$data->type)->orderBy('name','asc')->get();
            $subcategory = Category::where('status', 1)->where('parent_id','=',$data->cat_id)->where('product_type',$data->type)->orderBy('name','asc')->get();
            
            // dd($data);
            return view('product.edit', compact('data','idStr','category','subcategory','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }
    
    /**
     * Update the specified product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idStr,$getQueryString='')
    {
        // dd($request->all());
        try {
            $id = Crypt::decrypt($idStr);
            $request->validate([
                'title' => 'required|max:100|unique:products,title,'.$id,
                'public_name' => 'nullable|max:100|unique:products,public_name,'.$id,
                'cat_id' => 'required|exists:categories,id',
                'subcat_id' => 'nullable|exists:categories,id',
                'set_of_pcs' => 'required|numeric',
                'service_level' => 'nullable|required_if:type,fg|in:customer,dealer',
                'type' => 'required|in:fg,sp',
                'warranty_status' => 'nullable',
                // 'warranty_period' => 'nullable|required_if:warranty_status,yes',
                'warranty_period' => 'nullable',
                'mop' => 'nullable',
                // 'comprehensive_warranty' => 'required_if:goods_type,chimney',
                'comprehensive_warranty' => 'nullable',
                // 'comprehensive_warranty_free_services' => 'required_if:goods_type,chimney',
                'comprehensive_warranty_free_services' => 'nullable',
                // 'extra_warranty' => 'required_if:goods_type,chimney',
                'extra_warranty' => 'nullable',
                'installable_amount' => 'nullable|required_if:goods_type,chimney|numeric',
                // 'motor_warranty' => 'required_if:goods_type,chimney',
                'motor_warranty' => 'nullable',
                'profit_percentage' => 'required_if:type,sp',
                // 'supplier_warranty_period' => 'required_if:type,fg'
            ],[
                'service_level.required_if' => 'Service level is required',
                // 'comprehensive_warranty.required_if' => 'Please add free service tenure',
                // 'comprehensive_warranty_free_services.required_if' => 'Please add no of free services',
                // 'extra_warranty.required_if' => 'Please add additional warranty tenure',
                // 'motor_warranty.required_if' => 'Please add motor warranty tenure',
                'installable_amount.required_if' => 'Please add installation amount',
                'installable_amount.numeric' => 'Installation amount must be numeric',
                'profit_percentage.required_if' => 'Please add profit percentage',
                // 'supplier_warranty_period.required_if' => 'Please add supplier warranty period'
            ]);
            $params = $request->except('_token');

            // dd($params);
            // if($params['type'] == 'fg' && $params['goods_type'] == 'general' && empty($params['warranty_status']) ){
            //     return redirect()->back()->withErrors(['warranty_status' => 'Please choose warranty status'])->withInput();
            // }

            if(isset($params['warranty_status'])){
                if($params['warranty_status'] == 'no'){
                    $params['warranty_period'] = NULL;
                }
            }
            
            unset($params['subcat_name']);
            $params['is_installable'] = isset($params['is_installable'])?$params['is_installable']:0;
            $params['is_amc_applicable'] = isset($params['is_amc_applicable'])?$params['is_amc_applicable']:0;
            $params['is_title_public_name_same'] = isset($params['is_title_public_name_same'])?$params['is_title_public_name_same']:0;
            $params['is_test_product'] = isset($params['is_test_product'])?$params['is_test_product']:0;
            $params['updated_at'] = date('Y-m-d H:i:s');

            $browser_name = isset($params['browser_name'])?$params['browser_name']:NULL;
            $navigator_useragent = isset($params['navigator_useragent'])?$params['navigator_useragent']:NULL;

            unset($params['browser_name']);
            unset($params['navigator_useragent']);

            $data = Product::where('id',$id)->update($params);

            addChangeLog(Auth::user()->id,$request->ip(),'edit_product',$browser_name,$navigator_useragent,$params);

            if (!empty($data)) {
                Session::flash('message', 'Product updated successfully');
                return redirect('/product/list?'.$getQueryString);
                // return redirect()->route('product.list');
            } else {
                return redirect()->route('product.edit',$id)->withInput($request->all());
            }
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    /**
     * Change Status the specified product from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function toggle_status(Request $request,$idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $product = Product::find($id);
            $message = "";
            if($product->status == 1){
                Product::where('id',$id)->update(['status'=>0]);
                $params['product_name'] = $product->title;
                $params['status'] = 0;
                $message = "Product deactivated successfully";
            } else {
                Product::where('id',$id)->update(['status'=>1]);
                $params['product_name'] = $product->title;
                $params['status'] = 1;
                $message = "Product activated successfully";
            }

            $browser_name = isset($request->browser_name)?$request->browser_name:NULL;
            $navigator_useragent = isset($request->navigator_useragent)?$request->navigator_useragent:NULL;

            addChangeLog(Auth::user()->id,$request->ip(),'product_change_status',$browser_name,$navigator_useragent,$params);


            Session::flash('message', $message);        
            // if(!empty($getQueryString)){            
            return redirect('/product/list?'.$getQueryString);
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function copy($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $data = Product::with('category','subcategory')->find($id);
            $category = Category::where('status', 1)->whereNull('parent_id')->where('product_type',$data->type)->orderBy('name','asc')->get();
            $subcategory = Category::where('status', 1)->where('parent_id','=',$data->cat_id)->where('product_type',$data->type)->orderBy('name','asc')->get();
            
            return view('product.copy', compact('data','id','category','subcategory','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    public function csv_upload(Request $request)
    {
        # code...
        return view('product.csv-upload');

    }

    public function submit_csv(Request $request)
    {
        # code...
        $request->validate([
            'csv' => 'required'
        ]);
        $params = $request->except('_token');
        $csv = $params['csv'];
        // dd($params);
        
        $rows = Excel::toArray([],$request->file('csv'));
        $data = $rows[0];

        $columns = $rows[0][0];        
        $myReqColumns = ['Type','Name','Brand','Category','Subcategory','MRP','HSN','GST','Warranty'];
        $reqColumnErr = false;
        foreach($columns as $col){
            if(!in_array($col,$myReqColumns)){
                $reqColumnErr = true;
            }
        }
        if($reqColumnErr){
            return  redirect()->back()->withErrors(['csv'=> "Missing column in file"])->withInput();
        }

        // dd($data);
        $myArr = array();
        foreach($data as $key => $item){
            if($key != 0){
                // dd($item[2]);
                if($item[2] == 'KGA'){
                    $myArr[] = array(
                        'type' => $item[0],
                        'name' => $item[1],
                        'brand' => $item[2],
                        'category' => $item[3],
                        'subcategory' => $item[4],
                        'mrp' => $item[5],
                        'hsn_code' => $item[6],
                        'gst' => $item[7],
                        'warranty' => $item[8]
                    );
                }
            }
        }

        foreach($myArr as $key => $value){
            $category = $value['category'];
            $subcategory = $value['subcategory'];
            $product_type = $value['type'];

            $category_id = $subcategory_id = null;
            // echo 'category:- '.$category.'<br/>';
            // echo 'subcategory:- '.$subcategory.'<br/>';
            $check_category = Category::whereNull('parent_id')->where('name','LIKE',$category)->where('product_type', $product_type)->first();
            if(!empty($check_category)){
                $category_id = $check_category->id;
                // echo 'category_id:- '.$check_category->id.'<br/>';
            } else {
                $category_id = Category::insertGetId([
                    'name' => $category,
                    'product_type' => $product_type
                ]);
            }
            if(!empty($subcategory)){
                if($subcategory != '#NA'){
                    $check_subcategory = Category::where('parent_id',$category_id)->where('name','LIKE',$subcategory)->where('product_type',$product_type)->first();
                    if(!empty($check_subcategory)){
                        $subcategory_id = $check_subcategory->id;
                        // echo 'subcategory_id:- '.$check_subcategory->id.'<br/>';
                    } else {
                        $subcategory_id = Category::insertGetId([
                            'name' => $subcategory,
                            'parent_id' => $category_id,
                            'product_type' => $product_type
                        ]);
                    }
                }                
            }
            

            // echo $category_id.'<br/>';
            // echo $subcategory_id.'<br/>';

            // die;

            $check_product = Product::where('title','LIKE', trim($value['name']))->first();

            // echo 'key:- '.$key;

            if(empty($check_product)){
                $productCreateArr = array(
                    'title' => trim($value['name']),
                    'public_name' => trim($value['name']),
                    'unique_id' => genAutoIncreNo(5,'products'),
                    'is_title_public_name_same' => 1,
                    'mop' => $value['mrp'],
                    'hsn_code' => $value['hsn_code'],
                    'gst' => $value['gst'],
                    'warranty_status' => !empty($value['warranty'])?'yes':'no',
                    'warranty_period' => !empty($value['warranty'])?$value['warranty']:'',
                    'type' => $product_type,
                    'cat_id' => $category_id,
                    'subcat_id' => $subcategory_id,
                    'created_at' => date('Y-m-d H:i:s')
                );    
                // echo '<pre>'; print_r($productCreateArr);
                Product::insert($productCreateArr);
            } else {
                $productUpdateArr = array(
                    'mop' => $value['mrp'],
                    'hsn_code' => $value['hsn_code'],
                    'gst' => $value['gst'],
                    'warranty_status' => !empty($value['warranty'])?'yes':'no',
                    'warranty_period' => !empty($value['warranty'])?$value['warranty']:'',
                    'type' => $value['type'],
                    'cat_id' => $category_id,
                    'subcat_id' => $subcategory_id,
                    'updated_at' => date('Y-m-d H:i:s')
                );
                // echo '<pre>'; print_r($productUpdateArr);
                Product::where('id',$check_product->id)->update($productUpdateArr);
            }

        }

        ### Changelog ###

        $browser_name = isset($params['browser_name'])?$params['browser_name']:NULL;
        $navigator_useragent = isset($params['navigator_useragent'])?$params['navigator_useragent']:NULL;

        addChangeLog(Auth::user()->id,$request->ip(),'upload_product_csv',$browser_name,$navigator_useragent,$myArr);
         

        Session::flash('message', 'CSV uploaded successfully');
        return redirect()->route('product.list');


    }

    public function change_status_ajax(Request $request)
    {
        # Ajax change status...
        $id = !empty($request->id)?$request->id:'';
        $product = Product::find($id);
        $message = $status = "";
        if($product->status == 1){
            Product::where('id',$id)->update(['status'=>0]);
            $message = "Product deactivated successfully";
            $status = "inactive";
        } else {
            Product::where('id',$id)->update(['status'=>1]);
            $message = "Product activated successfully";
            $status = "active";
        }
        return ['status'=>$status,'message'=>$message];
    }

    public function assign_spare_goods(Request $request,$idStr,$getQueryString='')
    {
        # Assign Goods To A Spare...
        try {
            $id = Crypt::decrypt($idStr);
            $data = Product::find($id);
            return view('product.assign-goods', compact('data','id','idStr','getQueryString'));   
        } catch ( DecryptException $e) {
            return abort(404);
        }        
    }

    public function save_spare_goods(Request $request,$idStr,$getQueryString='')
    {
        # Save Goods To A Spare...
        try {
            $id = Crypt::decrypt($idStr);

            $params = $request->except('_token');
            $goods_ids = !empty($params['goods_ids'])?$params['goods_ids']:array();
            // dd($params);

            $all_prev_goods = SpareGoods::where('spare_id',$id)->get()->toArray();

            
            if(!empty($all_prev_goods)){
                foreach($all_prev_goods as $prev){
                    if(!in_array($prev['goods_id'],$goods_ids)){
                        SpareGoods::where('spare_id',$id)->where('goods_id',$prev['goods_id'])->delete();
                    }
                }
            }
            foreach($goods_ids as $goods){            
                $existGoods = SpareGoods::where('spare_id',$id)->where('goods_id',$goods)->first();
                
                if(!empty($existGoods)){
                } else {
                    SpareGoods::insert([
                        'goods_id' => $goods,
                        'spare_id' => $id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }            
            }

            $browser_name = isset($params['browser_name'])?$params['browser_name']:NULL;
            $navigator_useragent = isset($params['navigator_useragent'])?$params['navigator_useragent']:NULL;
            unset($params['browser_name']);
            unset($params['navigator_useragent']);

            addChangeLog(Auth::user()->id,$request->ip(),'goods_assign_for_spare',$browser_name,$navigator_useragent,$params);

            Session::flash('message', 'Goods assigned successfully');
            return redirect('/product/list?'.$getQueryString);
                         
        } catch ( DecryptException $e) {
            return abort(404);
        } 
    }

    public function view_amc(Request $request,$idStr,$getQueryString='')
    {
        # Assign Goods To A Spare...
        try {
            $id = Crypt::decrypt($idStr);
            $product = Product::find($id);
            $amc = ProductAmc::where('product_id',$id)->get()->toArray();
            return view('product.amc', compact('product','amc','id','idStr','getQueryString'));   
        } catch ( DecryptException $e) {
            return abort(404);
        }    
    }

    public function save_amc(Request $request,$idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);

            // dd($request->all());

            $request->validate([
                'details.*.month_val' => 'required',
                'details.*.amount' => 'required',
            ],[
                'details.*.month_val.required' => 'Please choose time period',
                'details.*.amount.required' => 'Please add amount'
            ]);


            $params = $request->except('_token');
            $details = $params['details'];

            // dd($params);

            $monthArr = array();
            foreach($details as $detail){
                // echo($detail['month_val']).'<br/>';
                $monthArr[] = $detail['month_val'];
            }

            $hasDuplicates = count($monthArr) > count(array_unique($monthArr)); 
            // dd($hasDuplicates);
            if($hasDuplicates){
                return redirect()->back()->withErrors(['finalErrMsg' => 'Same Time Period Added '.count(array_unique($monthArr)). ' times'])->withInput();
            }
            // dd($params);

            $oldMonths = array();
            $old_data = ProductAmc::where('product_id',$id)->get()->toArray();
            if(!empty($old_data)){
                foreach($old_data as $old){
                    $oldMonths[] = $old['month_val'];
                    
                    
                    if(!in_array($old['month_val'],$monthArr)){
                                                
                        ProductAmc::where('product_id',$id)->where('month_val',$old['month_val'])->delete();
                    }

                }
            }

            // dd($oldMonths);
            foreach($details as $item){  

                $existAmcs = ProductAmc::where('product_id',$id)->where('month_val',$item['month_val'])->first();
                
                if(!empty($existAmcs)){
                    ProductAmc::where('id',$existAmcs->id)->update([
                        'amount' => $item['amount'],
                        'description' => $item['description'],
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                } else {
                    ProductAmc::insert([
                        'month_val' => $item['month_val'],
                        'amount' => $item['amount'],
                        'description' => $item['description'],
                        'product_id' => $id,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }            
            }


            ## Product Is AMC Applicable Updated

            Product::where('id',$id)->update(['is_amc_applicable'=>1]);

            ### Changelog ###

            $browser_name = isset($params['browser_name'])?$params['browser_name']:NULL;
            $navigator_useragent = isset($params['navigator_useragent'])?$params['navigator_useragent']:NULL;

            unset($params['browser_name']);
            unset($params['navigator_useragent']);

            addChangeLog(Auth::user()->id,$request->ip(),'save_amc_offers',$browser_name,$navigator_useragent,$params);
            
            Session::flash('message', 'AMC Details Saved Successfully');
            return redirect('/product/list?'.$getQueryString);

        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    public function remove_amc_offers(Request $request,$idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);

            ## Check the deleteable offer exists for customer request
            $pending_request_count = CustomerAmcRequest::where('product_id',$id)->where('is_availed', 0)->count();

            if($pending_request_count == 0){
                // ProductAmc::where('product_id',$id)->delete();
                // Product::where('id',$id)->update(['is_amc_applicable'=>0]);
                Session::flash('message', 'All AMC Offers Removed For This Item Successfully');
                return redirect('/product/list?'.$getQueryString);
            } else {
                return  redirect()->back()->withErrors(['finalErrMsg'=> "There are  ".$pending_request_count." AMC requests pending to customer for this item. Cannot remove at all. "])->withInput(); 
            }


            
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    public function csv_export(Request $request)
    {
        $search = !empty($request->search)?$request->search:'';
        $status = !empty($request->status)?$request->status:'';
        $type = !empty($request->type)?$request->type:'';
        $cat_id = !empty($request->cat_id)?$request->cat_id:'';
        $subcat_id = !empty($request->subcat_id)?$request->subcat_id:'';
        
        
        $data = Product::select('*');

        if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('title', 'LIKE','%'.$search.'%')
                ->orWhere('public_name','LIKE','%'.$search.'%');
            });
        }
        $typeName = "product";
        if(!empty($type)){
            if($type == 'fg'){
                $typeName = "goods";
            }else{
                $typeName = "spares";
            }
            
            $data = $data->where('type', $type);            
        }
        
        if(!empty($cat_id)){
            $data = $data->where('cat_id', $cat_id);
        }
        if(!empty($subcat_id)){
            $data = $data->where('subcat_id', $subcat_id);
        }
        if($status == 'active'){
            $data = $data->where('status', 1);
        } else if ($status == 'inactive'){
            $data = $data->where('status', 0);
        }
        $data = $data->orderBy('id','desc')->get();

        
        $myArr = array();
        if(!empty($data)){
            foreach($data as $item){
                $myArr[] = array(
                    'itemcode' => $item->unique_id,
                    'itemname' => $item->title,
                    'class_name' => !empty($item->category)?$item->category->name:'',
                    'group_name' => !empty($item->subcategory)?$item->subcategory->name:'',
                    'mop' => $item->mop,
                    'itemcode' => $item->unique_id,
                    'repair_charge' => $item->repair_charge,
                    'set_of_pcs' => $item->set_of_pcs,
                    'warranty_status' => $item->warranty_status,
                    'warranty_period' => $item->warranty_period,
                    'is_installable' => $item->is_installable,
                    'spare_type' => $item->spare_type,
                    'goods_type' => $item->goods_type,
                    'profit_percentage' => $item->profit_percentage,
                    'hsn_code' => $item->hsn_code
                );
            }
        }
        
        $fileName = date('Ymd')."-kgamaster";
        $cat_name = $subcat_name = "";
        if(!empty($type)){
            $fileName .= "-".$typeName."";
        }
        if(!empty($cat_id)){
            $cat_name = getSingleAttributeTable('categories','id',$cat_id,'name');
            $fileName .= "-".urlencode($cat_name)."";
        }
        if(!empty($subcat_id)){
            $subcat_name = getSingleAttributeTable('categories','id',$subcat_id,'name');
            $fileName .= "-".urlencode($subcat_name)."";
        }
        if(!empty($search)){
            $fileName .= "-".urlencode($search)."";
        }
        $fileName .= ".csv";


        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $dateColumn = array('','Date:- '.date('j M Y, l'));
        
        $spaceColumn1 = array('','','','','','','');
        $typeColumn = array('','Type:- '.ucwords($typeName));
        $catColumn = array('','Class:-'.$cat_name);
        $subcatColumn = array('','Group:-'.$subcat_name);
        
        if($type == 'fg'){
            $columns = array('#','ITEM NAME','CLASS NAME','MRP','REPAIR CHARGE','WARRANTY PERIOD','INSTALLABLE','HSN CODE');
            

            $callback = function() use($myArr, $dateColumn,$typeColumn,$catColumn,$spaceColumn1,$columns) {
                $file = fopen('php://output', 'w');            
                fputcsv($file, $dateColumn);
                fputcsv($file, $typeColumn);
                fputcsv($file, $catColumn);
                fputcsv($file, $spaceColumn1);
                fputcsv($file, $columns);
                                
                $i=1;
                foreach ($myArr as $arr) {  
                    $row['#'] = $i;
                    $row['ITEM NAME'] = $arr['itemname'];
                    $row['CLASS NAME'] = $arr['class_name'];
                    $row['MRP'] = 'Rs. '.number_format((float) $arr['mop'], 2, '.', ''); ;
                    $row['REPAIR CHARGE'] = 'Rs. '.number_format((float) $arr['repair_charge'], 2, '.', '');
                    $row['WARRANTY PERIOD'] = ($arr['warranty_status'] == 'yes')?$arr['warranty_period'].' month':' - ';
                    $row['INSTALLABLE'] = !empty($arr['is_installable'])?'YES':'NO';
                    $row['HSN CODE'] = $arr['hsn_code'];
                    
                    
                    fputcsv($file, array(   
                            $row['#'],
                            $row['ITEM NAME'],
                            $row['CLASS NAME'],
                            $row['MRP'],
                            $row['REPAIR CHARGE'],
                            $row['WARRANTY PERIOD'],
                            $row['INSTALLABLE'],
                            $row['HSN CODE']
                        )
                    ); 
                    $i++;               
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        } else if ($type == 'sp'){
            
            $columns = array('#','ITEM NAME','CLASS NAME','GROUP NAME','MRP','SET OF PCS','HSN CODE','PROFIT PERCENTAGE');

            $callback = function() use($myArr, $dateColumn,$typeColumn,$catColumn,$subcatColumn,$spaceColumn1,$columns) {
                $file = fopen('php://output', 'w');            
                fputcsv($file, $dateColumn);
                fputcsv($file, $typeColumn);
                fputcsv($file, $catColumn);
                fputcsv($file, $subcatColumn);
                fputcsv($file, $spaceColumn1);
                fputcsv($file, $columns);
                
                
                $i=1;
                foreach ($myArr as $arr) {  
                    $row['#'] = $i;
                    $row['ITEM NAME'] = $arr['itemname'];
                    $row['CLASS NAME'] = $arr['class_name'];
                    $row['GROUP NAME'] = $arr['group_name'];
                    $row['MRP'] = 'Rs. '.number_format((float) $arr['mop'], 2, '.', ''); ;
                    $row['SET OF PCS'] = $arr['set_of_pcs'];
                    $row['HSN CODE'] = $arr['hsn_code'];
                    $row['PROFIT PERCENTAGE'] = $arr['profit_percentage'];

                    
                    fputcsv($file, array(   
                            $row['#'],
                            $row['ITEM NAME'],
                            $row['CLASS NAME'],
                            $row['GROUP NAME'],
                            $row['MRP'],
                            $row['SET OF PCS'],
                            $row['HSN CODE'],
                            $row['PROFIT PERCENTAGE']
                        )
                    ); 
                    $i++;               
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        



    }

    public function add_goods_warranty(Request $request,$idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $product = Product::find($id);
            $dealer_type = !empty($request->dealer_type)?$request->dealer_type:'';
            $warranty_type = !empty($request->warranty_type)?$request->warranty_type:'';
            $goods_warranty = array();
            $spareIds = SpareGoods::where('goods_id',$id)->pluck('spare_id')->toArray();
            $spear_parts = Product::whereIn('id', $spareIds)
            ->orderBy('title')
            ->get();
            // if(!empty($dealer_type)){
            //     $goods_warranty = ProductWarranty::where('goods_id', $id)->where('dealer_type', $dealer_type)->where('warranty_type', $warranty_type)->first();
            // }
            
            // dd($goods_warranty);
            return view('product.add-warranty', compact('product','id','idStr','dealer_type','getQueryString','goods_warranty', 'spear_parts'));

        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }
    // public function add_goods_warranty(Request $request,$idStr,$getQueryString='')
    // {
    //     try {
    //         $id = Crypt::decrypt($idStr);
    //         $product = Product::find($id);
    //         $dealer_type = !empty($request->dealer_type)?$request->dealer_type:'';
    //         $goods_warranty = array();
    //         if(!empty($dealer_type)){
    //             $goods_warranty = GoodsWarranty::where('goods_id', $id)->where('dealer_type', $dealer_type)->first();
    //         }
            
    //         // dd($goods_warranty);
    //         return view('product.add-warranty', compact('product','id','idStr','dealer_type','getQueryString','goods_warranty'));

    //     } catch ( DecryptException $e) {
    //         return abort(404);
    //     }
        
    // }

    public function list_goods_warranty(Request $request,$idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $product = Product::find($id);
            $dealer_type = !empty($request->dealer_type)?$request->dealer_type:'';
           
            $goods_warranty_khosla = ProductWarranty::where('goods_id', $id)->where('dealer_type', 'khosla')->get();
            $goods_warranty_nonkhosla = ProductWarranty::where('goods_id', $id)->where('dealer_type', 'nonkhosla')->get();
            
            // dd($goods_warranty);
            return view('product.list-warranty', compact('product','id','idStr','dealer_type','getQueryString','goods_warranty_khosla','goods_warranty_nonkhosla'));

        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    public function remove_goods_warranty(Request $request,$id){
        try{
            $ProductWarranty =ProductWarranty::where('id', $id)->first();
            $userAgent = request()->header('User-Agent');
            $browser = $this->getBrowser($userAgent);
             addChangeLog(Auth::user()->id,$request->ip(),'remove_product_warranty',$browser,$userAgent,$ProductWarranty);
            Session::flash('message', 'Warranty removed Successfully');
            $ProductWarranty->delete();
            return redirect()->back();
        } catch ( DecryptException $e) {
            return abort(404);
        }
       
    }

    private function getBrowser($userAgent)
    {
        $browsers = ['Opera', 'Edge', 'Chrome', 'Safari', 'Firefox', 'MSIE', 'Trident'];

        foreach ($browsers as $browser) {
            if (stripos($userAgent, $browser) !== false) {
                if ($browser === 'MSIE' || stripos($userAgent, 'Trident') !== false) {
                    return 'Internet Explorer';
                }
                return $browser;
            }
        }

        return 'Unknown';
    }


    public function save_goods_warranty(Request $request,$idStr,$getQueryString='')
    {
       //  dd($request->all());
        try {
            $id = Crypt::decrypt($idStr);
            $product = Product::find($id);

            $request->validate([
                'warranty_type' => 'required',
                
                // Required if warranty_type is 'parts'
                'spear_parts' => 'required_if:warranty_type,parts',
                'parts_warranty' => 'required_if:warranty_type,parts',
                'additional_warranty_type' => 'required_if:warranty_type,additional',
                'number_of_cleaning' => 'required_if:warranty_type,cleaning|max:10',
				 'number_of_deep_cleaning' => 'required_if:warranty_type,deep_cleaning|max:10',
                // Required if warranty_type is 'comprehensive', 'additional', or 'cleaning'
                'general_warranty' => 'required_if:warranty_type,comprehensive|required_if:warranty_type,additional|required_if:warranty_type,cleaning|required_if:warranty_type,deep_cleaning',
            ]);
            
            
            $params = $request->except('_token');
             //dd($params);

            $goods_id = $params['goods_id'];
            $dealer_type = $params['dealer_type'];
            $warranty_type = $params['warranty_type'];

            $existWarranty = ProductWarranty::where('goods_id', $goods_id)->where('dealer_type', $dealer_type)->where('warranty_type', $warranty_type)->first();
            if(!empty($existWarranty)){
                $existPartWarranty = ProductWarranty::where('goods_id', $goods_id)->where('dealer_type', $dealer_type)->where('spear_id', $params['spear_parts'])->first();
               
                if($warranty_type=='parts' && !empty($existPartWarranty)){
                    ProductWarranty::where('id', $existPartWarranty->id)->update([
                        'warranty_type' => $params['warranty_type'],
                        'updated_by' => Auth::user()->id,
                        'spear_id' => $params['spear_parts'],
                        'warranty_period' => ($params['warranty_type'] == 'parts')?$params['parts_warranty']:$params['general_warranty'],
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }elseif($warranty_type=='parts'){
                    ProductWarranty::insert([
                        'goods_id' => $params['goods_id'],
                        'created_by' => Auth::user()->id,
                        'dealer_type' => $params['dealer_type'],
                        'warranty_type' => $params['warranty_type'],
                        'spear_id' => ($params['warranty_type'] == 'parts')?$params['spear_parts']:null,
                        'warranty_period' => ($params['warranty_type'] == 'parts')?$params['parts_warranty']:$params['general_warranty'],
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }else{
                    ProductWarranty::where('id', $existWarranty->id)->update([
                        'warranty_type' => $params['warranty_type'],
                        'updated_by' => Auth::user()->id,
                        'spear_id' => ($params['warranty_type'] == 'parts')?$params['spear_parts']:null,
                        'number_of_cleaning' => ($params['warranty_type'] == 'cleaning')?$params['number_of_cleaning']:0,
						'number_of_deep_cleaning' => ($params['warranty_type'] == 'deep_cleaning') ? $params['number_of_deep_cleaning'] : 0,
                        'additional_warranty_type' => ($params['warranty_type'] == 'additional')?$params['additional_warranty_type']:null,
                        'warranty_period' => ($params['warranty_type'] == 'parts')?$params['parts_warranty']:$params['general_warranty'],
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            } else {
                ProductWarranty::insert([
                    'goods_id' => $params['goods_id'],
                    'created_by' => Auth::user()->id,
                    'dealer_type' => $params['dealer_type'],
                    'warranty_type' => $params['warranty_type'],
                    'additional_warranty_type' => ($params['warranty_type'] == 'additional')?$params['additional_warranty_type']:null,
                    'spear_id' => ($params['warranty_type'] == 'parts')?$params['spear_parts']:null,
                    'number_of_cleaning' => ($params['warranty_type'] == 'cleaning')?$params['number_of_cleaning']:0,
					'number_of_deep_cleaning' => ($params['warranty_type'] == 'deep_cleaning') ? $params['number_of_deep_cleaning'] : 0,
                    'warranty_period' => ($params['warranty_type'] == 'parts')?$params['parts_warranty']:$params['general_warranty'],
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            Session::flash('message', 'Warranty saved Successfully');
            return redirect('/product/list-goods-warranty/' . Crypt::encrypt($goods_id) . '?' . $getQueryString);

        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    public function duplicate_warranty(Request $request){
        // dd($request->all());
        $dealer_type = $request->dealer_type;
        $goods_id = $request->goods_id;
        $oppositeDealerType = $dealer_type === 'khosla' ? 'nonkhosla' : 'khosla';

        // Delete all records with the opposite dealer type for the given goods_id
        ProductWarranty::where('goods_id', $goods_id)
        ->where('dealer_type', $oppositeDealerType)
        ->delete();


         // Fetch all records with the current dealer type
        $currentDealerData = ProductWarranty::where('goods_id', $goods_id)
        ->where('dealer_type', $dealer_type)
        ->get();

         // Duplicate records with the opposite dealer type
            foreach ($currentDealerData as $record) {
                $newRecord = $record->replicate(); // Create a copy of the current record
                $newRecord->dealer_type = $oppositeDealerType; // Set to the opposite dealer type
                $newRecord->save(); // Save the new record
            }
        return redirect()->back()->with('message', 'Warranty duplication for (' . ucwords($oppositeDealerType ). ') type completed successfully.');

        
    }
    



        
    
}
