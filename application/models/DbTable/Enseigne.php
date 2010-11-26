<?php

class Application_Model_DbTable_Enseigne extends Zend_Db_Table_Abstract
{


	protected $_name = 'enseignes';


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


	public function fetchAllProductsFromEnseigne( $id )
	{
		return $this->_db->select()
			->from( array( 'p' => 'products' ), array( 'id', 'name', 'poids' ) )
			->join( array( 'c' => 'enseignes' ), 'p.xenseigne = c.id', array( 'xenseigne' => 'name', 'xid' => 'id' ) )
			->where( 'c.id = ?', $id )
			->order( 'p.name ASC' )
			->query();
	}
}

