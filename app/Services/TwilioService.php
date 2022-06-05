<?php

namespace App\Services;

use App\SMS;
use GuzzleHttp\Client as Guzzle;
use Twilio\Rest\Client;

class TwilioService
{
	
    public static function sendSMS($number, $message)
    {    
        $client   = new Guzzle();
        $response = $client->request('POST', 'http://demo8211737.mockable.io/', [
            'number'  => $number,
            'message' => $message
        ]);		
        $response = $response->getBody();
        $response = json_decode($response, true);
        if (!is_array($response)) {
            throw new \Exception("Error sending message Twilio.");		
        }
        $sms = new SMS();
        $sms->code = $response['code'];
        $sms->details = $response['msg'];

        return $sms;
    }

}