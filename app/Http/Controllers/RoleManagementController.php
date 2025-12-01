<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Module;
use App\Models\RoleModuleRestriction;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class RoleManagementController extends Controller
{
    //

    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $accessUserManagment = userAccess(Auth::user()->role_id,1);
            if(!$accessUserManagment){
                abort(404);
            }
            
            return $next($request);
        });
        
    }

    public function index(Request $request)
    {
        $data = Role::whereNotIn('id',[1,3])->get();
        return view('roles.list', compact('data'));
    }

    public function restricted_modules(Request $request,$idStr)
    {
        try {
            $role_id = Crypt::decrypt($idStr);
            $role = Role::find($role_id);
            $modules = Module::where('status', 1)->get();
            $restricted_modules = RoleModuleRestriction::where('role_id',$role_id)->pluck('module_id')->toArray();
            return view('roles.restricted-modules', compact('role_id','role','modules','restricted_modules'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    public function save_restricted_modules(Request $request)
    {
        $params = $request->except('_token');
        $role_id = $params['role_id'];
        $module_ids = !empty($params['module_ids'])?$params['module_ids']:array();
        // dd($params);

        $all_prev_modules = RoleModuleRestriction::where('role_id',$role_id)->get()->toArray();

        
        if(!empty($all_prev_modules)){
            foreach($all_prev_modules as $prev){
                if(!in_array($prev['module_id'],$module_ids)){
                    RoleModuleRestriction::where('role_id',$role_id)->where('module_id',$prev['module_id'])->delete();
                }
            }
        }
        foreach($module_ids as $modules){            
            $existModules = RoleModuleRestriction::where('role_id',$role_id)->where('module_id',$modules)->first();
            
            if(!empty($existModules)){
            } else {
                RoleModuleRestriction::insert([
                    'module_id' => $modules,
                    'role_id' => $role_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }            
        }

        Session::flash('message', 'Saved successfully');
        return redirect('/role-management/list');
    }
}
