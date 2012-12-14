<?php

namespace WrapperMagick;

/**
 * Unit test for WrapperMagick
 */
class WrapperMagickTest extends \PHPUnit_Framework_TestCase
{

	protected $obj;

	public function setUp()
	{
		//$this->obj = new WrapperMagick('test.ing');
	}
	
	
	public function testRedimensionar()
	{
		$this->obj = $this->getMock('WrapperMagick\WrapperMagick', array('execCommand', 'getContentType'), array('test.ing'));
		$this->obj->expects($this->once())
			->method('getContentType')
			->will($this->returnValue('image/jpeg'));
	
		$output = $this->obj->redimensionar(123)->assemble('destino');
		echo $output . "\n";
	}
	
	
	/*public function testCortar($x, $y, $ancho, $alto) {
		$output = $this->obj->cortar(123, 234, 345, 456)->assemble();
		echo $output;
	}*/
}
