<?php

namespace App\Traits;

use Illuminate\Support\Str;
use App\Models\ServiceSchedule;

trait SystemTrait
{
    /**
     * block a service schedule temporary to allow payment
     *
     * @param  mixed $serviceSchedulesId
     * @return bool
     */
    public static function block($serviceSchedulesId): bool
    {

       return ServiceSchedule::where('id',$serviceSchedulesId)
                            ->where('status',1)
                            ->whereNull('client_id')
                            ->update(['client_id'=>auth()->user()->id]);

    }
   /**
     *unblock a service schedule if not successfully paid for
     *@param string|int serviceSchedulesId
     *
     * @return boolean
     */
    public static function unblock($serviceSchedulesId): bool {
       // dd($serviceSchedulesId);
        return ServiceSchedule::where('id',$serviceSchedulesId)
        ->where('status',1)
        ->where('client_id',auth()->user()->id)
        ->update(['client_id'=>Null]);
    }

    /**
     * generatePassword
     *
     * generate password-like string with default length of 10
     * @param  int $length
     *
     * @return string
     */
    public static function generatePassword($length=10): string {

         return Str::random($length);
     }

     /**
     * generatePassword
     *
     * generate password-like string with default length of 10
     * @param  int $length
     *
     * @return string
     */
     public static function generatePasswordHashed($length=10): string {
        //$string = static::$genetePassowrd($length);
            $string = Str::random($length);
            $password = bcrypt($string);
        return $password;
    }
}