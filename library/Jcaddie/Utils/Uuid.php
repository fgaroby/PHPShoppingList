<?php
final class Jcaddie_Utils_Uuid
{


	/**
	 * Generates an UUID
	 * 
	 * @author     Anis uddin Ahmad <admin@ajaxray.com>
	 * @param      string  an optional prefix
	 * @return     string  the formatted uuid
	 */
	public static function getUuid( $prefix = '' )
	{
		$chars = md5( uniqid( mt_rand(), true ) );
		$uuid = substr( $chars, 0, 8 ) . '-';
		$uuid .= substr( $chars, 8, 4 ) . '-';
		$uuid .= substr( $chars, 12, 4 ) . '-';
		$uuid .= substr( $chars, 16, 4 ) . '-';
		$uuid .= substr( $chars, 20, 12 );
		
		return $prefix . $uuid;
	}
}