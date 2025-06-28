<?php

namespace App\Traits;

use App\Models\PaymentTransaction;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

trait MakePaymentTrait
{
    public static function transfer($mobile_money_wallet, $payment_method, $amount, $details)
    {

        /* return [
                 "status" => "Approved",
                "code" => "000",
                "transaction_id" => "000000003317",
                "reason" => "Transaction successful!",
                "customer_id" => "0000006329471786",
                "desc" => "NMC-Payment-Service",
                "merchant_data" => null,
        ]; */
       $str = null ;//NotificationDescription::select('name')->first();
        $desc = 'N&MC Service Payment';
        $res = null;
        if ($str) {
            $desc = $str->name;
        }
       // dd($payment_method);
        // public function payment_api()
        /***
         * "status" => "Approved"
        "code" => "000"
        "transaction_id" => "610727811900"
        "reason" => "Transaction successful!"
        "customer_id" => "0000005518365869"
        "desc" => "NMC-Premium-Service-Payment"
        "merchant_data" => null
        600000 ** */

        $num = rand(10, 99) . auth()->user()->id . strtotime(date('dHmsyi'));
        $transaction_id = substr("000000000000{$num}", -12);
        $formatted_amount = (intVal($amount) * 100);
        $final_amount = substr("000000000000{$formatted_amount}", -12);
        /****
         * try catch
         *
         *
         */
        $errorMessage = 'Network related error occured. Try again later';
        try {

            $response = Http::withHeaders([
                "Authorization" => "Basic " . base64_encode('nursing5ee2381235ee0:NDEyZWRhMjhkYTczODhkMDdlYzJiNmFiOTAxOGRiMTA='),
                "Cache-Control" => "no-cache",
                "Content-Type" => "application/json",
            ])->retry(3, 600000, function (Exception $exception, PendingRequest $request,$res) {
               // dd($exception); // instanceof ConnectionException;
                $res = json_encode(['reason' => 'Network Error','code'=>'419']);
            })->post("https://prod.theteller.net/v1.1/transaction/process", [
                "merchant_id" => "TTM-00003666",
                "transaction_id" => "{$transaction_id}",
                "desc" => "{$desc}",
                "amount" => "{$final_amount}",
                "subscriber_number" => "{$mobile_money_wallet}", //'0530808128',
                "r-switch" => "{$payment_method}",
                "processing_code" => "000200",

            ]);
            
            $res = $response->json();
        

        }  catch (\Illuminate\Http\Client\ConnectionException $e) {

            $res = array('status'=>$errorMessage,'code'=>'419');
        }

       
        if (is_array($res) && array_key_exists('reason', $res)) {
            PaymentTransaction::create([
                "status" => $res["status"],
                "code" => $res["code"],
                "reason" => $res["reason"],
                'amount' => $formatted_amount,
                'details' => $details,
                "mobile_money_wallet" => $mobile_money_wallet,
                "transaction_id" => $transaction_id,

            ]);
            $res['amount']=$formatted_amount;
            $res['details']=$details;
            $res['status']=str_replace('_',' ',$res["status"]);
        }
        //dd($res);$sm = session('user'.auth()->user()->id.'x');
       // dd($sm->name);
       return $res;

    }
}
