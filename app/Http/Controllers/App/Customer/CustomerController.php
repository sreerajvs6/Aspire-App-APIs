<?php

namespace App\Http\Controllers\App\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;

use App\Models\Loan;
use App\Models\PaymentSchedule;
use App\Models\Payment;

class CustomerController extends Controller
{
    
    public function applyLoan(Request $request)
    {
       $cstmrid = $request->cstmrid;
       $amount = $request->amount;
       $term = $request->term;
       $schedule = $request->schedule;
       $status = "PENDING";

       $loandata = new Loan();
       $loandata->cstmrid   = $cstmrid;
       $loandata->amount    = $amount;
       $loandata->term      = $term;
       $loandata->schedule  = $schedule;
       $loandata->status    = $status;

       if($loandata->save())
       {
        $respons = array(
                            "responseCode"=>"00",
                            "responseMsg"=>"Loan Applied Successfully",
                        );
        return response($respons); 
       }
    }

    public function listCstLoans(Request $request)
    {
        $cstmrid = $request->cstmrid;

        $loans = Loan::where('cstmrid',$cstmrid)->get();

        $arrayLoans=[];
        foreach ($loans as $key => $value) 
        {
            
                $arrayLoans[$key]['loanid'] = $value->id;
                $arrayLoans[$key]['amount'] = $value->amount;
                $arrayLoans[$key]['term'] = $value->term;
                $arrayLoans[$key]['status'] = $value->status;
            
        }

        $respons = array(
                            "responseCode"=>"00",
                            "responseMsg"=>"Loans list",
                            "data"=>array("loans"=>$arrayLoans)
                        );
        return response($respons); 
    }
    
    public function paySchedules(Request $request)
    {
        $cstmrid = $request->cstmrid;
        $loanid = $request->loanid;
        $loanamount = $request->loanamount;
        $scheduleno = $request->scheduleno;
        $repayamount = $request->repayamount;
        $date = $request->date;

        $paydata = new Payment();
        $paydata->cstmrid = $cstmrid;
        $paydata->loanid = $loanid;
        $paydata->repay_schedule = $scheduleno;
        $paydata->repay_amount = $repayamount;
        $paydata->repay_date = $date;
        $paydata->status = "PAID";

        $paydata->save();
        
        $paytotal = Payment::where('cstmrid',$cstmrid)->where('loanid',$loanid)->sum('repay_amount');
     
        if($paytotal == $loanamount)
        {
            DB::table('loans')->where('id', '=', $loanid)->update(
            [
                'status' => "PAID"
            ]
            );

            DB::table('payment_schedules')->where('cstmrid', '=', $cstmrid)->where('loanid', '=', $loanid)->update(
            [
                'status' => "PAID"
            ]
            );

        }

        DB::table('payment_schedules')->where('cstmrid', '=', $cstmrid)->where('loanid', '=', $loanid)->where('schedule_no', '=', $scheduleno)->update(
        [
            'status' => "PAID"
        ]
        );

        $respons = array(
                            "responseCode"=>"00",
                            "responseMsg"=>"Schedule Paid Successfully"
                        );
        return response($respons); 
     
    }
}
