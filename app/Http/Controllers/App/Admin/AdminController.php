<?php

namespace App\Http\Controllers\App\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;

use App\Models\Loan;
use App\Models\Customer;
use App\Models\PaymentSchedule;

class AdminController extends Controller
{
    public function listLoans(Request $request)
    {
        $loans = Loan::orderBy('id','DESC')->get();
    
        $arrayLoans=[];
        
        foreach ($loans as $key => $value) 
        {
            $arrayLoans[$key]['cstmrid'] = $value->cstmrid;

            $cstmrdata = Customer::where('id',$value->cstmrid)->first();

            $arrayLoans[$key]['fname'] = $cstmrdata->fname;
            $arrayLoans[$key]['lname'] = $cstmrdata->lname;
            $arrayLoans[$key]['phone'] = $cstmrdata->phone;
            $arrayLoans[$key]['email '] = $cstmrdata->email;
            $arrayLoans[$key]['job '] = $cstmrdata->job;

            $arrayLoans[$key]['loanid'] = $value->id;
            $arrayLoans[$key]['amount'] = $value->amount;
            $arrayLoans[$key]['term'] = $value->term;
            $arrayLoans[$key]['schedule'] = $value->schedule;
            $arrayLoans[$key]['status'] = $value->status;
        
        }
        $respons = array(
                            "responseCode"=>"00",
                            "responseMsg"=>"Loan Datas",
                            "data"=>array("loans"=>$arrayLoans)
                        );
        return response($respons);

    }

    public function viewLoan(Request $request)
    {
        $cstmrid = $request->cstmrid;
        $loanid = $request->loanid;

        $loans = Loan::where('id',$loanid)->first();
        if($loans->status == "APPROVED")
        {
            $emidata = PaymentSchedule::where('cstmrid',$cstmrid)->where('loanid',$loanid)->get();
            $arrayEmis = [];
                    
            foreach ($emidata as $key => $emi) 
            {
                $arrayEmis[$key]['repay_amount'] = $emi->repay_amount;
                $arrayEmis[$key]['schedule_no'] = $emi->schedule_no;
                $arrayEmis[$key]['schedule_date'] = $emi->schedule_date;
                $arrayEmis[$key]['status'] = $emi->status;
            }

            $respons = array(
                                "responseCode"=>"00",
                                "responseMsg"=>"Loan Schedules",
                                "data"=>array("emis"=>$arrayEmis)
                            );
            return response($respons);
        }
        else
        {
            $respons = array(
                            "responseCode"=>"02",
                            "responseMsg"=>"Pending Approval"
                        );
            return response($respons);
        }

    }

    public function statusChange(Request $request)
    {
        $cstmrid = $request->cstmrid;
        $loanid = $request->loanid;
        $schedule = $request->schedule;
        $status = $request->status;

        $loandata = Loan::where('id',$loanid)->where('cstmrid',$cstmrid)->first();
        $amntrqst = $loandata->amount;
        $termrqst = $loandata->term;
        
        $exp = explode(",",$schedule);

        $emi = $amntrqst/$termrqst;

        if(is_float($emi))
        {    
            $emi = round($emi, 2);

            $less = $termrqst - 1;

            for($i = 0 ; $i< $less ; $i++)
            {
                $pymntschdls = new PaymentSchedule();    
                $pymntschdls->cstmrid = $cstmrid;
                $pymntschdls->loanid = $loanid;
                $pymntschdls->repay_amount = $emi;
                $pymntschdls->schedule_date = $exp[$i];
                $pymntschdls->schedule_no = $i+1;
                $pymntschdls->status = "PENDING";
                $pymntschdls->save();
            }

            $mult = $emi * $less;
            $diff = $amntrqst - $mult;

            $pymntschdls = new PaymentSchedule();
            $pymntschdls->cstmrid = $cstmrid;
            $pymntschdls->loanid = $loanid;
            $pymntschdls->repay_amount = $diff;
            $pymntschdls->schedule_date = $exp[$less];
            $pymntschdls->schedule_no = $termrqst;
            $pymntschdls->status = "PENDING";
            $pymntschdls->save();
        }
        else
        {
            for($i = 0 ; $i< $termrqst ; $i++)
            {
                $pymntschdls = new PaymentSchedule();    
                $pymntschdls->cstmrid = $cstmrid;
                $pymntschdls->loanid = $loanid;
                $pymntschdls->repay_amount = $emi;
                $pymntschdls->schedule_date = $exp[$i];
                $pymntschdls->schedule_no = $i+1;
                $pymntschdls->status = "PENDING";
                $pymntschdls->save();
            }
        }

        DB::table('loans')->where('id', '=', $loanid)->update(
        [
            'status' => $status
        ]
        );

        $respons = array(
                            "responseCode"=>"00",
                            "responseMsg"=>"Status Changed Successfully",
                        );
        return response($respons); 

    }
}
