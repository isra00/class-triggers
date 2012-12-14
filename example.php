<?php

include 'vendor/autoload.php';

use ClassTriggers\ClassTriggers;

class Coche {

	private $posicion = 0;
	
	public function avanzar($incremento)
	{
		$this->posicion += $incremento;
	}
	
	public function retroceder()
	{
		$this->posicion--;
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
		echo 'No puedes avanzar más de 10. Deteniendo ejecución del método';
		return ClassTriggers::COND_STOP_EXECUTION;
	}
});


$cocheInterceptado->bind('avanzar', 'postMethod', function(&$arguments, $output) {
	echo "Has avanzado $arguments[0] -> " . $this->posicion . "\n";
});

$cocheInterceptado->avanzar(3);
$cocheInterceptado->avanzar(3);
$cocheInterceptado->avanzar(3);
$cocheInterceptado->avanzar(3);
