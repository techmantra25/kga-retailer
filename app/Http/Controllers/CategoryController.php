<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use File; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class CategoryController extends Controller
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
     * Display a listing of the category or any child category.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = !empty($request->search)?$request->search:'';
        $type = !empty($request->type)?$request->type:'parent';
        $status = !empty($request->status)?$request->status:'all';
        $product_type = !empty($request->product_type)?$request->product_type:'';
        $paginate = 20;


        $total = Category::whereNull('parent_id')->count();        
        $totalActive = Category::whereNull('parent_id')->where('status', 1)->count();
        $totlInactive = Category::whereNull('parent_id')->where('status', 0)->count();
        
        

        $data = Category::select('*')->with('child');
        $totalResult = Category::select('*')->with('child');
        
        $data = $data->where(function($query) use ($search){
            $query->where('name', 'LIKE','%'.$search.'%')->orWhereHas('child', function ($q) use ($search) {
                $q->where('name', 'LIKE','%'.$search.'%');
            });
        });
        $totalResult = $totalResult->where(function($query) use ($search){
            $query->where('name', 'LIKE','%'.$search.'%')->orWhereHas('child', function ($q) use ($search) {
                $q->where('name', 'LIKE','%'.$search.'%');
            });
        });

        if(!empty($type)){
            if($type == 'parent'){
                // die('parent');
                $data = $data->where('parent_id','=',null);
                $totalResult = $totalResult->where('parent_id','=',null);

                $total = Category::whereNull('parent_id')->count();        
                $totalActive = Category::whereNull('parent_id')->where('status', 1)->count();
                $totlInactive = Category::whereNull('parent_id')->where('status', 0)->count();
            } else if ($type == 'child'){
                // die('child');
                $data = $data->where('parent_id','!=',null);
                $totalResult = $totalResult->where('parent_id','!=',null);

                $total = Category::whereNotNull('parent_id')->count();        
                $totalActive = Category::whereNotNull('parent_id')->where('status', 1)->count();
                $totlInactive = Category::whereNotNull('parent_id')->where('status', 0)->count();
            }
        }

        if(!empty($product_type)){
            $data = $data->where('product_type',$product_type);
            $totalResult = $totalResult->where('product_type',$product_type);
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

        $data = $data->appends([
            'search'=>$search,
            'type'=>$type,
            'product_type'=>$product_type,
            'status'=>$status,
            'page'=>$request->page
        ]);
        return view('category.list', compact('data','totalResult','total','totalActive','totlInactive','status','search','type','product_type','paginate'));
    }

    /**
     * Show the form for creating a new category.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $type = !empty($request->type)?$request->type:'parent';
        $parents = Category::where('parent_id','=',null)->where('status',1)->get();
        return view('category.add', compact('parents','type'));
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            // 'parent_id' => 'exists:categories,id|nullable',
            'parent_id' => 'nullable|required_if:type,child',
            'name' => 'required|unique:categories,name'
        ],[
            'parent_id.required_if' => 'Please choose a category',
            'name.required' => 'Please enter name'
        ]);

        $params = $request->except('_token');

        $uplaod_base_url_prefix = config('app.uplaod_base_url_prefix');

        if(!empty($params['photo'])){
            $upload_path = $uplaod_base_url_prefix."uploads/category/";
            $image = $params['photo'];
            $imageName = time() . "." . $image->getClientOriginalName();
            $image->move($upload_path, $imageName);
            $uploadedImage = $imageName;
            $params['image'] = $upload_path . $uploadedImage;
            unset($params['photo']);
        } else {
            $params['image'] = '';
        }

        $params['created_at'] = date('Y-m-d H:i:s');
		//Amc applicable
		$params['amc_applicable'] = isset($params['amc_applicable']) ?? 0; 
        

        // $params['parent_id'] = !empty($params['parent_id'])?$params['parent_id']:0;
        // dd($params);
        unset($params['type']);

        $id = Category::insertGetId($params);
        $message = "";
        if(empty($params['parent_id'])){
            $message = "Category Created Successfully";
            Session::flash('message', $message);
            return redirect()->route('category.list',['type'=>'parent']);
        } else {
            $message = "Sub Category Created Successfully";
            Session::flash('message', $message);
            return redirect()->route('category.list',['type'=>'child']);
        }

        
    }

    /**
     * Display the specified category.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$idStr,$getQueryString='')
    {
        try {
            $type = !empty($request->type)?$request->type:'parent';
            $id = Crypt::decrypt($idStr);
            $data = Category::find($id);
            return view('category.detail', compact('data','id','getQueryString','type'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    /**
     * Show the form for editing the specified category.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$idStr,$getQueryString='')
    {
        try {
            $type = !empty($request->type)?$request->type:'parent';
            $id = Crypt::decrypt($idStr);
            $data = Category::find($id);
            $parents = Category::where('parent_id','=',null)->where('status',1)->get();
            return view('category.edit', compact('type','data','idStr','parents','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    /**
     * Update the specified category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idStr,$getQueryString='')
    {
        try {
            // echo config('app.uplaod_base_url_prefix'); die;
            $uplaod_base_url_prefix = config('app.uplaod_base_url_prefix');
            $id = Crypt::decrypt($idStr);
            $request->validate([
                'parent_id' => 'exists:categories,id|nullable',
                'name' => 'unique:categories,name,'.$id
            ]);
    
            $params = $request->except('_token');
    
            $category = Category::find($id);
    
            if(!empty($params['photo'])){            
                File::delete($category->image);
    
                $upload_path = $uplaod_base_url_prefix."uploads/category/";
                $image = $params['photo'];
                $imageName = time() . "." . $image->getClientOriginalName();
                $image->move($upload_path, $imageName);
                $uploadedImage = $imageName;
                $params['image'] = $upload_path . $uploadedImage;
                unset($params['photo']);            
    
            } else {
                $params['image'] = $category->image;
            }

            $params['updated_at'] = date('Y-m-d H:i:s');
    
            // dd($params); die;
            $params['parent_id'] = !empty($params['parent_id'])?$params['parent_id']:NULL;
            $data = Category::where('id',$id)->update($params);
    
            $message = "";
            if(empty($params['parent_id'])){
                $message = "Parent Category Updated Successfully";
            } else {
                $message = "Child Category Updated Successfully";
            }
    
            Session::flash('message', $message);
            return redirect('/category/list?'.$getQueryString);
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /**
     * Change Status the specified category from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function toggle_status($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $category = Category::find($id);
            $message = "";
            if($category->status == 1){
                Category::where('id',$id)->update(['status'=>0]);
                $message = "Category deactivated successfully";
            } else {
                Category::where('id',$id)->update(['status'=>1]);
                $message = "Category activated successfully";
            }
            Session::flash('message', $message);        
            if(!empty($getQueryString)){            
                return redirect('/category/list?'.$getQueryString);
            }
            return redirect()->route('category.list');
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }
}
