<?php

namespace App\Traits;

use App\Models\User;

trait GetUserDetails
{
    /**
     * byReferrenceId
     *
     * @param  mixed $referrenceId | $email
     * @return $data
     */
    public static function byReferrenceId($param)
    {

        $data = User::Where('email',$param)
                            ->selectRaw('id,name,email,telephone')
                            ->first();

        //check if the record is for the same user
        if(auth()->user()->email==$data->email)
         {
            return null;
         }

        return $data;

    }
}