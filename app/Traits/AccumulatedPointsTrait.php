<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\AccumulatedPoint;

trait AccumulatedPointsTrait
{
    /**
     * get
     *
     **retrives the valid accumulated CPD points for the current user
     * @return array
     * returns array of min_cpd_points,total_cpd
     */
    public static function get(): array{

        $start_year = Carbon::parse('first day of January')->format('Y-m-d');
        $end_year = Carbon::parse('last day of January next year')->format('Y-m-d');
        /* $res = AccumulatedPoint::select(DB::raw("institution, SUM(points) AS total"))
        ->whereDate('date_completed', '>=', $start_year)
        ->whereDate('date_completed', '<=', $end_year)
        ->groupBy('institution')
        ->get(); */
        $min_cpd_points = 15;
        $total_cpd = 0.0;
        $res = AccumulatedPoint::whereDate('date_completed', '>=', $start_year)
            ->whereDate('date_completed', '<=', $end_year)->sum('points');

        if (auth()->user()->rank) {
            $min_cpd_points = auth()->user()->rank->cpd_points;
        }
        // dd($res);
        $str = [];

        if ($res) {
            //$total_cpd = $res;
            $total_cpd = number_format(($res / 100), 1);

        } /* */

        return ['min_cpd_points'=>$min_cpd_points,'total_cpd'=>$total_cpd];
    }
}