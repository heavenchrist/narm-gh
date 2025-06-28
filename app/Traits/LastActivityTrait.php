<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Stevebauman\Location\Facades\Location;

trait LastActivityTrait
{
    public static function save()
    {
       // Cache::forget('location'.auth()->user()->id);

        ini_set('max_execution_time', 180);//3 minutes
        //loopback ip
        $loopbackIP = '127.0.0.1';
        $defaultLocation = 'Accra, Ghana';
       // $location = Location::get('115.240.90.163');
       // dd($location);
       // dd(Auth);

        //''
        //115.240.90.163
        //request()->ip()
        if (auth()->check()) {

            if (!Cache::has('location'.auth()->user()->id)) {
                    $userPrevLocation = str_contains(strtolower(auth()->user()->location),'ghana');

        //if previous location is gh check current location and compare
                    //check if location is set already
                    if (!$userPrevLocation) {

                            //check if the user's IP is loopback
                            if (request()->ip() == $loopbackIP) {

                                $userLocation = $defaultLocation;
                                $user = auth()->user();
                                $user->timestamps    = false;
                                $user->location = $userLocation;
                                $user->last_activity = now()->getTimestamp();
                                $user->saveQuietly();

                                self::updateUserLcationCache();
                                }
                                else {

                                    self::updateUserLocation();

                             }


                    }else{
                    // if (request()->ip() != $loopbackIP && $userPrevLocation == true ) {
                            if (request()->ip() == $loopbackIP && $userPrevLocation == true ) {

                                self::updateUserLocation();

                            }

                    }

             }

         }
    }


    public static function updateUserLocation(){

        try {
                //$newLocation = Location::get('41.210.11.223'); //
                $location = Location::get(request()->ip());

                if ($location) {
                    $userLocation = $location->cityName.', '.$location->countryName;
                }


                $user = auth()->user();
                $user->timestamps    = false;
                $user->last_activity = now()->getTimestamp();
                    if($userLocation){

                        $user->location = $userLocation;

                    }

                $user->saveQuietly();


        self::updateUserLcationCache();
        } catch (\Throwable $th) {



        }
    }


    public static function updateUserLcationCache(){
        $userPrevLocation = str_contains(strtolower(auth()->user()->location),'ghana');

        if($userPrevLocation){
            //GH local user
            Cache::forget('location'.auth()->user()->id);
            Cache::put('location'.auth()->user()->id, auth()->user()->location, now()->addHours(6)); //6 hours
        }else{
            //Outside GH user
            Cache::forget('location'.auth()->user()->id);
            Cache::put('location'.auth()->user()->id, auth()->user()->location, now()->addMonths(6)); //6 months
        }
    }

}