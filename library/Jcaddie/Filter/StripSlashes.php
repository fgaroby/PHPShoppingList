<?php
/**
 * Strip slashes if magic_quote_gpc is activated
 * 
 * @package jcaddie
 * @subpackage filter
 */
class Jcaddie_Filter_StripSlashes implements Zend_Filter_Interface
{


	/**
	 * Défini dans Zend_Filter_Interface
	 *
	 * Returns $value without newline control characters
	 *
	 * @param  string $value
	 * @return string
	 */
	public function filter( $value )
	{
		if( get_magic_quotes_gpc() )
		{
			return stripslashes( $value );
		}
		return $value;
	}
}

