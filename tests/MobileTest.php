<?php

namespace Tests;

use Mockery as m;
use App\Mobile;
use App\Call;
use App\Contact;
use App\Services\ContactService;
use App\Interfaces\CarrierInterface;
use PHPUnit\Framework\TestCase;

class MobileTest extends TestCase
{	
	/** @test */
	public function it_returns_null_when_name_empty()
	{
		$provider = m::mock(CarrierInterface::class);
		$mobile   = new Mobile($provider);

		$this->assertNull($mobile->makeCallByName(''));
	}

	/** @test */
	public function it_returns_call_when_calling_name()
	{
		$call 	 	   	     = m::mock('overload:'.Call::class);
		$contact 	     	 = m::mock('overload:'.Contact::class);
		$contact->name   	 = "Yeimy";
		$contact->lastName   = "Acuña";

		$provider = m::mock(CarrierInterface::class);
		$provider->shouldReceive('dialContact')->withArgs([$contact]);
		$provider->shouldReceive('makeCall')->andReturn($call);

		m::mock('alias:'.ContactService::class)
			->shouldReceive('findByName')
			->withArgs(['Yeimy'])
			->andReturn($contact);
		$mobile = new Mobile($provider);

		$this->assertInstanceOf(Call::class, $mobile->makeCallByName('Yeimy'));
	}
	
	/** @test */	
	public function it_returns_exception_when_contact_not_found()
	{		
		$provider = m::mock(CarrierInterface::class);
		m::mock('alias:'.ContactService::class)
			->shouldReceive('findByName')->withArgs(['Yeimy']);			
		$this->expectException(\Exception::class);

		$mobile = new Mobile($provider);
		$mobile->makeCallByName('Yeimy');
	}

}
