<?php

include 'vendor/autoload.php';

use ClassTriggers\ClassTriggers;

class Coche 
{

	private $posicion = 0;
	
	public function avanzar($incremento)
	{
		$this->posicion += $incremento;
	}
	
	public function getPosicion()
	{
		return $this->posicion;
	}
}

$cocheInterceptado = new ClassTriggers(new Coche);

$cocheInterceptado->bind('avanzar', 'preMethod', function(&$arguments) {
	echo 'Has solicitado avanzar(' . $arguments[0] . ")\n";
	
	if ($this->posicion + $arguments[0] > 10) {
		echo "No puedes avanzar más de 10. Deteniendo ejecución del método\n";
		return ClassTriggers::COND_STOP_EXECUTION;
	}
});


$cocheInterceptado->bind('avanzar', 'postMethod', function(&$arguments, $output) {
	if ($this->posicion > 10)
	{
		$this->posicion = 10;
	}
});

$cocheInterceptado->avanzar(3);
echo $cocheInterceptado->getPosicion() . "\n";
$cocheInterceptado->avanzar(3);
echo $cocheInterceptado->getPosicion() . "\n";
$cocheInterceptado->avanzar(3);
echo $cocheInterceptado->getPosicion() . "\n";
$cocheInterceptado->avanzar(3);
echo $cocheInterceptado->getPosicion() . "\n";

$cocheInterceptado = new ClassTriggers(new Coche);

$cocheInterceptado->bind('avanzar', 'postMethod', function(&$arguments) {
	if ($this->posicion > 10)
	{
		$this->posicion = 10;
	}
	return ClassTriggers::NO_MORE_ACTIONS;
});

$cocheInterceptado->bind('avanzar', 'postMethod', function(&$arguments) {
	$this->posicion = 99;
});

$cocheInterceptado->avanzar(11);
echo $cocheInterceptado->getPosicion() . "\n";
