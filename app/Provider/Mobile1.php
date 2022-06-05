<?php

namespace App\Providers;

use App\Interfaces\CarrierInterface;

class Mobile1 implements CarrierInterface
{
    public function dialContact(Contact $contact)
    {        
    }

    public function makeCall()
    {        
    }

    public function sendSMS(string $number, string $body)
    {        
    }
}