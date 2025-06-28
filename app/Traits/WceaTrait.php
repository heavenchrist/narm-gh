<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait WceaTrait
{
    /**
     * get WCEA CPD points using the AIN/PIN
     * getPoints
     *
     * @param  string $pin_ain
     * @return array
     */
    public static function getPoints($pin_ain): array
    {
        $timestamp = date('c');

        //echo  12EF15436112 '<br>' . $x = date(DATE_ATOM, time()); // an RFC-2822 or ISO-8601 formatted representation of the current time
                $verb = 'GET';
                $request_URI = 'v1.1/report/elearningActivity/?q=(report_mobile[eq]:all,wcea_organization_uid[eq]:' . $pin_ain . ')';
        // removing all spaces
                $token = preg_replace("/\s+/", "", $timestamp . $verb . $request_URI);

                $api_secret = 'a1edaaa6176e58149c6e6762033b7127';
                $signature = hash_hmac('sha256', $token, $api_secret);
                $api_key = 'd855f3588d35995dccfaf02b740d6c2d';
                $base_url = 'http://wceaapi.org/' . $request_URI;
                $data = [];

                $errorMessage = 'Network connection error occured. Try again later';

                try {
                    //code...
                    $response = Http::withHeaders([
                        "Request-Time" => $timestamp,
                        "Api-Key" => $api_key,
                        "Signature" => $signature,
                        "Cache-Control" => "no-cache",
                        "Content-Type" => "application/json",
                    ])->timeout(600000)
                        ->get($base_url);
                    $data = $response->json();
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    //dd($e->getMessage());
                    $data= array('error'=>$errorMessage);
                }

                return $data;
    }


    public static function checkSubscription()
    {
        //get current user pin
        $timestamp = date('c');

        //echo  12EF15436112 '<br>' . $x = date(DATE_ATOM, time()); // an RFC-2822 or ISO-8601 formatted representation of the current time
                $verb = 'GET';
                $request_URI = 'lookup?pin_no='.auth()->user()->pin_ain;

                //https://api.wcea.education/lookup?reg_no=REG_NUM&pin_no=PIN_NUM&email=EMAIL&payment_ref=PAYMENT_REF
        // removing all spaces
                $token = preg_replace("/\s+/", "", $timestamp . $verb . $request_URI);

                $api_secret = 'a1edaaa6176e58149c6e6762033b7127';
                $signature = hash_hmac('sha256', $token, $api_secret);
                $api_key = 'd855f3588d35995dccfaf02b740d6c2d';
                // $base_url = 'http://wceaapi.org/' . $request_URI;
                $base_url = 'https://api.wcea.education/' . $request_URI;

                $response = Http::withHeaders([
                    "Request-Time" => $timestamp,
                    "Api-Key" => $api_key,
                    "Signature" => $signature,
                    "Cache-Control" => "no-cache",
                    "Content-Type" => "application/json",
                ])->timeout(600000)
                    ->get($base_url);
                $data = $response->json();
                //return $data;
                return $data[0]['activity'][0]['activity_date'];

               /*  array:1 [▼
                    0 => array:12 [▼
                        "id" => "253748"
                        "planType" => "None"
                        "username" => "12EF15436112-GH"
                        "registration_number" => "RMN3101"
                        "PIN" => "12EF15436112"
                        "firstname" => "Emmanuel"
                        "lastname" => "Febiri"
                        "email" => "e.febiri14@gmail.com"
                        "expiry_date" => null
                        "password" => null
                        "payments" => []
                        "activity" => array:1 [▼
                        0 => array:2 [▼
                            "activity_date" => "2022-07-26 00:00:00"
                            "country" => "GH"
                        ]
                        ]
                    ]
                    ] */
    }
}