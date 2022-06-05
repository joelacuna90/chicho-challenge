<?php

namespace App;

use App\Interfaces\CarrierInterface;
use App\Services\ContactService;


class Mobile
{

	protected $provider;
	
	function __construct(CarrierInterface $provider)
	{
		$this->provider = $provider;
	}


	public function makeCallByName($name = '')
	{
		if ( empty($name) ) {
			return;
		}
		$contact = ContactService::findByName($name);		
		if (!($contact)) {
			throw new \Exception("No contact was found by name.");		
		}

		$this->provider->dialContact($contact);

		return $this->provider->makeCall();
	}

	public function sendSMS($number = '', $body = '')
	{		
		if ( !($number) or !($body) ) {
			throw new \Exception("the phone number and body are required to send an SMS.");
		}		
		$validateNumber = ContactService::validateNumber($number);		
		if (!$validateNumber) {
			throw new \InvalidArgumentException("The phone number is invalid.");
		}

		return $this->provider->sendSMS($number, $body);
	}

}
