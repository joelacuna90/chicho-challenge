<?php

namespace Tests;

use Mockery as m;
use App\Mobile;
use App\Call;
use App\SMS;
use App\Contact;
use App\Services\ContactService;
use App\Providers\Mobile1;
use App\Providers\Mobile2;
use App\Interfaces\CarrierInterface;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class MobileTest extends TestCase
{	
	protected $provider;
	protected $providerMovil1;
	protected $providerMovil2;

	protected function setUp(): void
	{		
		$this->provider = m::mock(CarrierInterface::class);
		$this->providerMovil1 = m::mock('overload:'.Mobile1::class, CarrierInterface::class);
		$this->providerMovil2 = m::mock('overload:'.Mobile2::class, CarrierInterface::class);		
	}

	/** @test */
	public function it_returns_null_when_name_empty()
	{		
		$mobile   = new Mobile($this->provider);

		$this->assertNull($mobile->makeCallByName(''));
	}

	/** @test */
	public function it_returns_call_when_calling_name()
	{
		$call 	 	   	     = m::mock('overload:'.Call::class);
		$contact 	     	 = m::mock('overload:'.Contact::class);
		$contact->name   	 = "Yeimy";
		$contact->lastName   = "Acuña";
		
		$this->provider->shouldReceive('dialContact')->withArgs([$contact]);
		$this->provider->shouldReceive('makeCall')->andReturn($call);

		m::mock('alias:'.ContactService::class)
			->shouldReceive('findByName')
			->withArgs(['Yeimy'])
			->andReturn($contact);
		$mobile = new Mobile($this->provider);

		$this->assertInstanceOf(Call::class, $mobile->makeCallByName('Yeimy'));
	}
	
	/** @test */	
	public function it_returns_exception_when_contact_not_found()
	{				
		m::mock('alias:'.ContactService::class)
			->shouldReceive('findByName')->withArgs(['Yeimy'])->andReturn(null);	
		$this->expectException(\Exception::class);

		$mobile = new Mobile($this->provider);
		$mobile->makeCallByName('Yeimy');
	}
	
	/** @test */	
	public function it_send_sms_for_number()
	{		
		$sms = m::mock('overload:'.SMS::class);		
		$this->provider->shouldReceive('sendSMS')
			->withArgs(['99218989', 'Test message body.'])
			->andReturn($sms);
		m::mock('alias:'.ContactService::class)
			->shouldReceive('validateNumber')
			->withArgs(['99218989'])
			->andReturn(true);
		$mobile = new Mobile($this->provider);

		$this->assertInstanceOf(SMS::class, $mobile->sendSMS('99218989', 'Test message body.'));
	}

	/** @test */
	public function it_returns_call_mobile1_provider()
	{
		$call 			   = m::mock('overload:'.Call::class);
		$contact 		   = m::mock('overload:'.Contact::class);
		$contact->name 	   = "Yeimy";
		$contact->lastName = "Acuña";
		$contact->number   = "992189089";		
		$this->providerMovil1->shouldReceive('dialContact')->withArgs([$contact]);
		$this->providerMovil1->shouldReceive('makeCall')->andReturn($call);
		m::mock('alias:'.ContactService::class)
			->shouldReceive('findByName')
			->withArgs(['Yeimy'])
			->andReturn($contact);
		$mobile = new Mobile($this->providerMovil1);

		$this->assertInstanceOf(Call::class, $mobile->makeCallByName('Yeimy'));
	}

	/** @test */
	public function it_returns_call_mobile2_provider()
	{
		$call 			   = m::mock('overload:'.Call::class);
		$contact 		   = m::mock('overload:'.Contact::class);
		$contact->name 	   = "Yeimy";
		$contact->lastName = "Acuña";
		$contact->number   = "992189089";		
		$this->providerMovil2->shouldReceive('dialContact')->withArgs([$contact]);
		$this->providerMovil2->shouldReceive('makeCall')->andReturn($call);		
		m::mock('alias:'.ContactService::class)
			->shouldReceive('findByName')
			->withArgs(['Yeimy'])
			->andReturn($contact);
		$mobile = new Mobile($this->providerMovil2);

		$this->assertInstanceOf(Call::class, $mobile->makeCallByName('Yeimy'));
	}


}
