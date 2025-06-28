<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait ShortNameTrait
{
    public static function change($stringValue){
        $str = implode('', array_map(function ($value) {
            if ($value) {
                return $value[0];
            }

        },
            explode(' ', trim($stringValue))
        ));
        
       return Str::upper($str);
    }
}
