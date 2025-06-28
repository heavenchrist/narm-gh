<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait TokenGenerator
{
    /**
     * create unique uuid
     *
     * @return string
     */
    public static function create(): string
    {

        return Str::uuid();
    }


    /**
     * create a Special Code
     *
     * @param  int $length
     * @param  string $sperator
     * @return string
     */
    public static function createSpecialCode($length=3,$sperator='-'): string
    {
        $code = strtotime(now());
        $specialCode = substr($code, 0, $length).$sperator.substr($code, 3, $length).$sperator.substr($code, 6);
        return  $specialCode;
    }
}