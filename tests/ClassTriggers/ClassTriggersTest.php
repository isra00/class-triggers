<?php

namespace ClassTriggers;

class Car 
{
	private $position = 0;
	
	public function forward($increment)
	{
		$this->position += $increment;
	}
	
	public function getPosition()
	{
		return $this->position;
	}
}

class ClassTriggersTest extends \PHPUnit_Framework_TestCase
{

	protected $obj;

	public function setUp()
	{
		$this->obj = new ClassTriggers(new Car);
	}
	
	public function testPreMethodStopExecution()
	{
		$this->obj->bind('forward', 'preMethod', function(&$arguments) {
			if ($this->position + $arguments[0] > 10) {
				return ClassTriggers::COND_STOP_EXECUTION;
			}
		});
		
		$this->obj->forward(100);
		$this->assertEquals(0, $this->obj->getPosition());
	}
}
