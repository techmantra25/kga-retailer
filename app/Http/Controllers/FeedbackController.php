<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Validator;
use App\Models\Installation;
use App\Models\InstallationFeedback;
use App\Models\Repair;
use App\Models\RepairFeedback;
use App\Models\Maintenance;
use App\Models\MaintenanceFeedback;

class FeedbackController extends Controller
{
    //

    /* ++++++++++++++++++++++++++++++++INSTALLATION+++++++++++++++++++++++++++++++++ */

    public function form_installation(Request $request)
    {
        # feedback installation form...
        
        $id = $request->id;
        $data = Installation::findOrFail($id);
        $customer_name = $data->customer_name;
        $mobile_no = $data->mobile_no;
        $bill_no = $data->bill_no;
        // $isFeedbackAdded = false;
        $installation_feedback = InstallationFeedback::where('installation_id',$id)->first();
        $isFeedbackAdded = !empty($installation_feedback)?true:false;
        return view('feedback.installation-form', compact('customer_name','mobile_no','bill_no','id','isFeedbackAdded'));
    }

    public function submit_installation(Request $request)
    {
        # submit installation feedback and show thank you page...
        $request->validate([
            'feedback' => 'required'
        ],[
            'feedback.required' => 'Please give feedback'
        ]);
        $params = $request->except('_token');
        $feedback = $params['feedback'];
        $installation_id = $params['installation_id'];
        $params['created_at'] = date('Y-m-d H:i:s');
        $installation_feedback = InstallationFeedback::where('installation_id',$installation_id)->first();
        $isFeedbackAdded = !empty($installation_feedback)?true:false;

        if($isFeedbackAdded){
            return  redirect()->back()->withErrors(['feedback'=> "Feedback already submitted"])->withInput(); 
            
        }
        // dd($params);

        $installation = Installation::find($installation_id);
        $product_id = $installation->product_id;
        $params['product_id'] = $product_id;

        InstallationFeedback::insert($params);
        

        return redirect()->route('feedback.thankyou-installation');
        
    }

    public function thankyou_installation()
    {
        # code...
        return view('feedback.installation-success');
    }

    /* ++++++++++++++++++++++++++++++++REPAIR+++++++++++++++++++++++++++++++++ */


    public function form_repair(Request $request)
    {
        # feedback repair form...
        // dd($request->all());
        $id = $request->id;
        $data = Repair::findOrFail($id);
        $customer_name = $data->customer_name;
        $customer_phone = $data->customer_phone;
        $bill_no = $data->bill_no;
        // $isFeedbackAdded = false;
        $repair_feedback = RepairFeedback::where('repair_id',$id)->first();
        $isFeedbackAdded = !empty($repair_feedback)?true:false;
        return view('feedback.repair-form', compact('customer_name','customer_phone','bill_no','id','isFeedbackAdded'));
    }

    public function submit_repair(Request $request)
    {
        # submit repair feedback and show thank you page...
        $request->validate([
            'feedback' => 'required'
        ],[
            'feedback.required' => 'Please give feedback'
        ]);
        $params = $request->except('_token');
        $feedback = $params['feedback'];
        $repair_id = $params['repair_id'];
        $params['created_at'] = date('Y-m-d H:i:s');
        $repair_feedback = RepairFeedback::where('repair_id',$repair_id)->first();
        $isFeedbackAdded = !empty($repair_feedback)?true:false;

        if($isFeedbackAdded){
            return  redirect()->back()->withErrors(['feedback'=> "Feedback already submitted"])->withInput(); 
            
        }
        // dd($params);

        $repair = Repair::find($repair_id);
        $product_id = $repair->product_id;
        $params['product_id'] = $product_id;

        RepairFeedback::insert($params);
        

        return redirect()->route('feedback.thankyou-repair');
        
    }

    public function thankyou_repair()
    {
        # code...
        return view('feedback.repair-success');
    }

    /* ++++++++++++++++++++++++++++++++MAINTENANCE+++++++++++++++++++++++++++++++++ */


    public function form_maintenance(Request $request)
    {
        # feedback maintenance form...
        
        $id = $request->id;
        $data = Maintenance::findOrFail($id);
        $customer_name = $data->customer_name;
        $customer_phone = $data->customer_phone;
        $bill_no = $data->bill_no;
        // $isFeedbackAdded = false;
        $maintenance_feedback = MaintenanceFeedback::where('maintenance_id',$id)->first();
        $isFeedbackAdded = !empty($maintenance_feedback)?true:false;
        return view('feedback.maintenance-form', compact('customer_name','customer_phone','bill_no','id','isFeedbackAdded'));
    }

    public function submit_maintenance(Request $request)
    {
        # submit maintenance feedback and show thank you page...
        $request->validate([
            'feedback' => 'required'
        ],[
            'feedback.required' => 'Please give feedback'
        ]);
        $params = $request->except('_token');
        $feedback = $params['feedback'];
        $maintenance_id = $params['maintenance_id'];
        $params['created_at'] = date('Y-m-d H:i:s');
        $maintenance_feedback = MaintenanceFeedback::where('maintenance_id',$maintenance_id)->first();
        $isFeedbackAdded = !empty($maintenance_feedback)?true:false;

        if($isFeedbackAdded){
            return  redirect()->back()->withErrors(['feedback'=> "Feedback already submitted"])->withInput(); 
            
        }
        // dd($params);

        $maintenance = Maintenance::find($maintenance_id);
        $product_id = $maintenance->product_id;
        $params['product_id'] = $product_id;

        MaintenanceFeedback::insert($params);
        

        return redirect()->route('feedback.thankyou-maintenance');
        
    }

    public function thankyou_maintenance()
    {
        # code...
        return view('feedback.maintenance-success');
    }


}
