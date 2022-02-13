<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Auth;

use App\Models\User;
use App\Models\Customer;

class AspireAppController extends Controller
{
    public function createCustomer(Request $request)
    {
        $fname = $request->fname;
        $lname = $request->lname;
        $phone = $request->phone;
        $email = $request->email;
        $job   = $request->job;
        $password = Hash::make($request->password);
        $usertype = "customer";

        $cstmrdata = new Customer();
        $cstmrdata->fname = $fname;
        $cstmrdata->lname = $lname;
        $cstmrdata->phone = $phone;
        $cstmrdata->email = $email;
        $cstmrdata->job = $job;

        $checkmail = Customer::where('email',$email)->get();
        if(count($checkmail) > 0)
        {
            $respons = array(
                              "responseCode"=>"02",
                              "responseMsg"=>"Email already used",
                            );
             return response($respons); 
        }
        else
        {    
            $cstmrdata->save();

            $userdata = new User();
            $userdata->name = $fname;
            $userdata->email = $email;
            $userdata->password = $password;
            $userdata->user_type = $usertype;

            if($userdata->save())
            {
                 $respons = array(
                                  "responseCode"=>"00",
                                  "responseMsg"=>"Customer Registered Successfully",
                                );
                 return response($respons); 
            }
        }
    }

    public function login(Request $request)
    {
          
                if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) 
                {   
                    $token=Hash::make(mt_rand());
                    
                    $id = Auth::user()->id;
                    $user_type = Auth::user()->user_type;
                    
                    $update = User::find(Auth::user()->id);
                    $update->remember_token = $token;
                    $update->save();
                    
                    $cstmrdata = Customer::where('email',$request->email)->first();

                    if($user_type == "customer")
                    {
                        $respons = array(
                              "responseCode"=>"00",
                              "responseMsg"=>"Logined Successfully",
                              "data"=>array("type"=>"customer","token"=>$token,"customerid"=>$cstmrdata->id,"name"=>$cstmrdata->fname.' '.$cstmrdata->lname,"phone"=>$cstmrdata->phone,"job"=>$cstmrdata->job)
                            );
                        return response($respons); 
                    }
                    else
                    {
                        $respons = array(
                              "responseCode"=>"00",
                              "responseMsg"=>"Logined Successfully",
                              "data"=>array("type"=>"admin","token"=>$token,"name"=>"Admin")
                            );
                        return response($respons); 
                    }
                } 
                else
                {
                      $respons = array(
                              "responseCode"=>"02",
                              "responseMsg"=>"Invalid Credentials",
                            );
                        return response($respons);   
                }
    }

    
}
