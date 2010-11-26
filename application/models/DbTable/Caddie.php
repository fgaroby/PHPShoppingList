<?php

class Application_Model_DbTable_Caddie extends Zend_Db_Table_Abstract
{


	protected $_name = 'caddies';


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
	
	
	/**
	 * 
	 * Renvoie tous les produits contenus dans le caddie
	 */
	public function fetchAllProductsFromCaddie()
	{
		return $this->_db->select()
			->from( array( 'cad' => 'caddies' ), array( 'xcaddie' => 'id', 'caddie' => 'name' ) )
			->join( array( 'cp' => 'caddies_products' ), 'cad.id = cp.xcaddie' )
			->join( array( 'p' => 'products' ), 'p.id = cp.xproduct', array( 'xproduct' => 'id', 'product' => 'name' ) )
			->join( array( 'cat' => 'categories' ), 'p.xcategory = cat.id', array( 'xcategory' => 'id', 'category' => 'name' ) )
			->order( 'p.name ASC' )
			->query();
	}
}

