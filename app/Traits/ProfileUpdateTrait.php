<?php

namespace App\Traits;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Renewal;
use App\Models\RegistrationNumber;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;

trait ProfileUpdateTrait
{
    public static function getNmcGhanaUpdate()
    {

        //dd(auth()->user()->pin_ain); //16CA06731809
        $status = false;
        $errorMessage = 'A network related error occured!';
        $feedback = null;
        $data = [];
        try {
            //code...
            $response = Http::withoutVerifying()->withHeaders([
                "Cache-Control" => "no-cache",
                "Content-Type" => "application/json",
            ])
               //->post("https://nmi.nmc.gov.gh/cpds-online/get_cpd_points", [
                ->post("https://154.160.75.101/cpds-online/get_cpd_points", [
                    "pin_ain" => auth()->user()->pin_ain,
                ]);
            $data = $response->json();
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
           // $data= $e->getMessage();
            $data= array('error'=>$errorMessage);
        }
        ///
         //dd($data);
        /////////////////
        if (array_key_exists("message", $data)) {
            //dd($data);
            if ($data['message'] === 'success') {
                $card_details = $data['data'][0];
                unset($data['data'][0]);
                $registration_details = $data['data'];
                //get the issued and expiry dates diff and loop
                $issued = $issued_date = Carbon::parse($card_details['issued_date']);
                $valid_until = Carbon::parse($card_details['valid_until']);
                $expiry_date = Carbon::parse($card_details['expiry_date']);
                $years = $issued_date->diffInYears($valid_until);
                //dd($expiry_date);
                //update card renewal details
                //$date->copy()->endOfYear()
                // Renewal::where('user_id', auth()->user()->id)->delete();
                for ($i = 0; $i <= $years; $i++) {
                    //$issued = $issued_date->addYears($i);
                    if ($i > 0) {
                        $issued = $issued->addYear();
                    }
                    $valid = $issued->copy()->endOfYear();
                    Renewal::updateOrCreate(
                        [
                            'user_id' => auth()->user()->id,
                            'registration_number' => $card_details['registration_num'],
                            'valid_until' => $valid->format('Y-m-d'),
                        ], [
                            'expiry_date' => $expiry_date->format('Y-m-d'),
                            'issued_date' => $issued->format('Y-m-d'),
                            'QR_code' => $card_details['QR_code'],
                        ]);
                }

                if (is_array($registration_details)) {
                    $registration_details = array_values($registration_details);
                    if (count($registration_details[0]) > 0) {

                        //update current user details
                        // dd($registration_details);

                        // $registration_details = Arr::flatten($registration_details);

                        // $contains = Arr::isList($registration_details[0]);
                        // dd($contains);
                        $genderId = $registration_details[0][0]['gender'];
                       $gender = null;
                        switch ($genderId) {
                          case $genderId==1:
                           $gender='Male';
                            break;
                          case $genderId==2:
                            $gender='Female';
                            break;

                          default:
                          $gender = null;
                        }
                        //dd($gender);

                         //updating user details
                        User::where('id', auth()->user()->id)->update(
                            [
                                'index_number' => $registration_details[0][0]['index_number'],
                                'gender' => $gender,
                                'date_of_birth' => $registration_details[0][0]['date_of_birth'],
                                'name' => $card_details['name'],
                            ]
                        );
                        //updating registration records
                        //RegistrationNumber::whereBelongsTo(auth()->user()->id)->delete();
                        foreach ($registration_details[0] as $row) {
                            RegistrationNumber::updateOrCreate(
                                [
                                    'user_id' => auth()->user()->id,
                                    'program' => $row['program'],
                                ],
                                [
                                    'registration_date' => $row['registration_date'],
                                    'registration_number' => $row['reg_number'],
                                    'school' => $row['school'],

                                ]);
                        }
                        $status = true;
                        $feedback = 'Update was successful!';

                    } else {
                        // dd('No');
                        //error
                        $status = false;
                        $feedback = 'Sorry! Some Registration details not available';
                    }
                } else {
                    //error
                    $status = false;
                    $feedback = 'Sorry! Some Registration details not available';
                }

            } else {
                //error
                $status = false;
                $feedback = $data['data'];
            }

            //send success message

        } else {
            $status = false;
            $feedback = $errorMessage;
        }

        //send an error message
        // return $response->json();03ES29503905

        if ($status) {
            Notification::make()
                ->title($feedback)
                ->success()->send();
            /*  ->persistent()
            ->actions([
            Action::make('ok')->label('click here to continue')
            ->button()->url($this->getResource()::getUrl('view', ['record' => $this->record])),

            ]) */
            //

        } else {
            Notification::make()
                ->title($feedback)
                ->danger()
                ->seconds(10)
                ->send();
        }

    }
}