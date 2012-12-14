Inject logic to any method of any PHP class, dinamically
========================================================

[![Build Status](https://secure.travis-ci.org/isra00/class-triggers.png)](http://travis-ci.org/isra00/class-triggers)

This is a partial and experimental implementation of Aspect-Oriented Programming. It gives you the ability to capture and manipulate the inputs, outputs and execution of any method of any class in PHP.

Basic usage
-----------

	$a = new AnyClass;
	$a = new ClassTriggers($a);

	$a->bind($targetMethod, $trigger, function($arguments) {
		//Injected logic.
	});


Example
-------

	use ClassTriggers\ClassTriggers;

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

	$interceptedCar = new ClassTriggers(new Car);
	
	$interceptedCar->bind('forward', 'preMethod', function(&$arguments) {
		echo 'You have requested forward(' . $arguments[0] . ")\n";
	
		if ($this->position + $arguments[0] > 10) {
			echo "Can't forward more than 10. Stopping method execution.\n";
			return ClassTriggers::COND_STOP_EXECUTION;
		}
	});

More info
---------

I wrote a tutorial in spanish:

http://israelviana.es/inyectar-logica-en-los-metodos-de-cualquier-clase-php-dinamicamente/

Requiremens
-----------

 * PHP 5.4
 * Composer
 
Installation
------------

You can install it via Composer, and use it instantly thanks to the Composer autoloader.
