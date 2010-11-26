<?php
/**
 * Enregistrement (Row) de base de données
 */
class Jcaddie_Db_Table_Row extends Zend_Db_Table_Row_Abstract
{


	/**
	 * Activation / désactivation de l'autosauvegarde à la destruction
	 */
	private static $_autoSave = true;


	/*protected function _insert()
	{
		if( ! $this->_data['id'] )
			$this->_data['id'] = $this->_uuid();
		$log = Zend_Registry::get( 'log' );
		$log->info( Zend_Debug::dump( $this->_data, "INSERT: $this->_tableClass", false ) );
	}*/


	/**
	 * Manipulation de l'autosauvegarde
	 */
	public static function setAutoSave( $save )
	{
		self::$_autoSave = ( bool ) $save;
	}


	/**
	 * Appelé à la désérialisation de l'objet
	 * Reconnecte automatiquement l'objet à sa table
	 */
	public function __wakeup()
	{
		$this->setTable( new $this->_tableClass() );
	}
}