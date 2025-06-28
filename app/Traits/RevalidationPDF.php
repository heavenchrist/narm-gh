<?php

namespace App\Traits;

use Illuminate\Support\Str;
use App\Models\Revalidation;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Users\UserRevalidation;
use Illuminate\Support\Facades\Storage;

trait RevalidationPDF
{
    /**
     * generate
     *
     * @param  mixed $revalidationId
     * @return bool
     */
    public static function generate($revalidationId): bool
    {
        // Generate Revalidation PDF here


        $data = Revalidation::withoutGlobalScopes()->where('id',$revalidationId)
        ->with(
             [
                 'client:id,name',
                  /**/'practiceHoursLog'=>function($q){
                     return $q->with(['workSetting','scopeOfPractice','registration','specialisation']);
                 },
                 'professionalDevelopment'=>function($qr){
                     return $qr->with(['organisation','trainingMode','topic','scopeOfPractice']);
                 },
                 'feedbackLog'=>function($qr){
                     return $qr->with(['sourceOfFeedback','typeOfFeedback']);
                 },
                 'reflectiveAccounts',
                 'reflectiveDiscussions'=>function($qr){
                     return $qr->with(['discussant']);
                 },
                 'revalidationConfirmations'=>function($qr){
                     return $qr->with(['confirmerType']);
                 },



             ]
         )->first();

         $pdf = Pdf::loadView('pdf.revalidation-pdf', ['data'=>$data])->setPaper('a4', 'landscape');

             $pdf->output();

             $domPdf = $pdf->getDomPDF();



             $canvas = $domPdf->getCanvas();
             $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
                 if($pageNumber>1){
                 $number = $pageNumber - 1;
                 $count = $pageCount -1;
                 $text = "Page $number of $count";
                 $font = $fontMetrics->getFont('Helvetica');
                 $pageWidth = $canvas->get_width();
                 $pageHeight = $canvas->get_height();
                 $size = 10;
                 $width = $fontMetrics->getTextWidth($text, $font, $size);
                 $canvas->text($pageWidth - $width - 20, $pageHeight - 20, $text, $font, $size);
                 }
             });
                 /* Set Page Number to Header
             $canvas->page_text(10, 10, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);*/



             /* Set Page Number to Footer */

           // $canvas->page_text(10, $canvas->get_height() - 20, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);

           $filename =  Str::ulid(now()).'.pdf';



Storage::put('public/revalidation_uploads/'.$filename, $pdf->output());

           if($pdf){
//save the link into the db
$data->document_url = 'revalidation_uploads/'.$filename;
        $data->save();

              return true;
         }
         else{
         return false;
         }

    }
}