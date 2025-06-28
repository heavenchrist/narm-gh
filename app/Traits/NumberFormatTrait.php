<?php

namespace App\Traits;

trait NumberFormatTrait
{
    /**
     * short
     *
     * @param  mixed $number
     * @param  mixed $precision
     * @return string
     */
    public static function short($number, $precision = 1): string {
        if ($number < 900) {
            // 0 - 900
            $number_format = number_format($number, $precision);
            $suffix = '';
        } elseif ($number < 900000) {
            // 0.9k-850k
            $number_format = number_format($number * 0.001, $precision);
            $suffix = 'K';
        } elseif ($number < 900000000) {
            // 0.9m-850m
            $number_format = number_format($number * 0.000001, $precision);
            $suffix = 'M';
        } elseif ($number < 900000000000) {
            // 0.9b-850b
            $number_format = number_format($number * 0.000000001, $precision);
            $suffix = 'B';
        } else {
            // 0.9t+
            $number_format = number_format($number * 0.000000000001, $precision);
            $suffix = 'T';
        }

        // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
        // Intentionally does not affect partials, eg "1.50" -> "1.50"
        if ($precision > 0) {
            $dotzero = '.' . str_repeat('0', $precision);
            $number_format = str_replace($dotzero, '', $number_format);
        }

        return $number_format . $suffix;
    }
}