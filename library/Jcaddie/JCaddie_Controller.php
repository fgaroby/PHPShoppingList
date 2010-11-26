<?php
class Jcaddie_Controller extends Zend_Controller_Action
{


	public function __call( $method, $args )
	{
		if( 'Action' == substr( $method, - 6 ) )
		{
			// If the action method was not found, render the error
			// template
			return $this->render( 'error' );
		}
		// all other methods throw an exception
		


		throw new Exception( 'Invalid method "' . $method . '" called', 500 );
	}
}