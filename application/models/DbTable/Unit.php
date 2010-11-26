<?php

class Application_Model_DbTable_Unit extends Zend_Db_Table_Abstract
{


	protected $_name = 'units';


	protected $_primary = 'id';


	protected $_sequence = false;


	protected $_rowClass = 'Jcaddie_Db_Table_Row';


	public function insert( array $data )
	{
		// Génération de l'id, si celui-ci n'existe pas encore
		if( empty( $data['id'] ) )
			$data['id'] = Jcaddie_Utils_Uuid::getUuid();
		
		return parent::insert( $data );
	}
}

