<?php


/** 
 * @author windu
 * 
 * 
 */
class Application_Model_Product
{


	protected $_id;


	protected $_name;


	protected $_poids;


	protected $_category;


	function __construct( array $options = null )
	{
		if( is_array( $options ) )
			$this->setOptions( $options );
	}


	public function __set( $name, $value )
	{
		$method = 'set' . $name;
		if( ( 'mapper' == $name ) || ! method_exists( $this, $method ) )
			throw new Exception( 'Invalid product property' );
		
		$this->$method( $value );
	}


	public function __get( $name )
	{
		$method = 'get' . $name;
		if( ( 'mapper' == $name ) || ! method_exists( $this, $method ) )
			throw new Exception( 'Invalid product property' );
		
		return $this->$method();
	}


	public function setOptions( array $options )
	{
		$methods = get_class_methods( $this );
		foreach( $options as $key => $value )
		{
			$method = 'set' . ucfirst( $key );
			if( in_array( $method, $methods ) )
				$this->$method( $value );
		}
		
		return $this;
	}


	public function setId( $id )
	{
		$this->id = $id;
		
		return $this;
	}


	public function getId()
	{
		return $this->_id;
	}
}


?>