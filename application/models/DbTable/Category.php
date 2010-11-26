<?php

class Application_Model_DbTable_Category extends Zend_Db_Table_Abstract
{


	protected $_name = 'categories';


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


	public function fetchAllProductsFromCategory( $id )
	{
		return $this->_db->select()
			->from( array( 'p' => 'products' ), array( 'id', 'name', 'poids' ) )
			->join( array( 'c' => 'categories' ), 'p.xcategory = c.id', array( 'xcategory' => 'name', 'xid' => 'id' ) )
			->where( 'c.id = ?', $id )
			->order( 'p.name ASC' )
			->query();
	}
}

