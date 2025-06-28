<?php

namespace App\Traits;

use Illuminate\Support\Str;
use App\Models\Users\UserRevalidation;

trait RevalidationCompletedToSubmit
{
    /**
     * check
     *
     * @param  mixed $revalidationId
     * @param  mixed $data
     * @return bool|array
     */
    public static function check($revalidationId,$data=false){



    $results = UserRevalidation::withoutGlobalScopes()->with(
        [
           'practiceHoursLog',
            'professionalDevelopment',
            'feedbackLog',
            'reflectiveAccounts',
            'reflectiveDiscussions',
            'revalidationConfirmations',

       ])
   ->latest()->first();
   //prepare info
   $output = [];
   $shoudlShow = true;

    if($results->practiceHoursLog->count() > 0){
        $output[] = 'Practice Hours Log: '.$results->practiceHoursLog->count().' '.Str::plural('entry',$results->practiceHoursLog->count()).' made';
    }else{
        $output[] = 'Practice Hours Log:<font color="red"> No '.Str::plural('entry',$results->practiceHoursLog->count()).' made</font>';
        $shoudlShow = false;
    }
    if($results->professionalDevelopment->count() > 0){
        $output[] = '<br>CPD: '.$results->professionalDevelopment->count().' '.Str::plural('entry',$results->professionalDevelopment->count()).' made';
    }
    else{
        $output[] = '<br>CPD: <font color="red">No '.Str::plural('entry',$results->professionalDevelopment->count()).' made</font>';
        $shoudlShow = false;
    }
    if($results->feedbackLog->count() > 0){
        $output[] = '<br>Feedback Log: '.$results->feedbackLog->count().' '.Str::plural('entry',$results->feedbackLog->count()).' made';
    }
    else{
        $output[] = '<br>Feedback Log: <font color="red">No '.Str::plural('entry',$results->feedbackLog->count()).' made</font>';
        $shoudlShow = false;
    }
    if($results->reflectiveAccounts->count() > 0){
        $output[] = '<br>Reflective Accounts:'. $results->reflectiveAccounts->count().' '.Str::plural('entry',$results->reflectiveAccounts->count()).' made';
    }
    else{
        $output[] = '<br>Reflective Accounts: <font color="red">No '.Str::plural('entry',$results->reflectiveAccounts->count()).' made</font>';
        $shoudlShow = false;
    }
    if($results->reflectiveDiscussions->count() > 0){
        $output[] = '<br>Reflective Discussions: '. $results->reflectiveDiscussions->count().' '.Str::plural('entry',$results->reflectiveDiscussions->count()).' made';
    }
    else{
        $output[] = '<br>Reflective Discussions: <font color="red">No '.Str::plural('entry',$results->reflectiveDiscussions->count()).' made</font>';
        $shoudlShow = false;
    }
    if($results->revalidationConfirmations->count() > 0){
        $output[] = '<br>Revalidation Confirmations: '. $results->revalidationConfirmations->count().' '.Str::plural('entry',$results->revalidationConfirmations->count()).' made';
    }
    else{
        $output[] = '<br>Revalidation Confirmations: <font color="red">No '.Str::plural('entry',$results->revalidationConfirmations->count()).' made</font>';
        $shoudlShow = false;
    }

    if($data){
        return $output;
    }
    else{
        return $shoudlShow;
    }
    }
}