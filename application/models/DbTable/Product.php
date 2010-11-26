<?php

class Application_Model_DbTable_Product extends Zend_Db_Table_Abstract
{


	protected $_name = 'products';


	protected $_primary = 'id';


	protected $_sequence = false;


	protected $_rowClass = 'Jcaddie_Db_Table_Row';


	protected $_referenceMap = array( 'category' => array( 'columns' => 'id', 'refTableClass' => 'Application_Model_DbTable_Category' ) );


	public function insert( array $data )
	{
		// Génération de l'id, si celui-ci n'existe pas encore
		if( empty( $data['id'] ) )
			$data['id'] = Jcaddie_Utils_Uuid::getUuid();
		
		return parent::insert( $data );
	}


	/**
	 * 
	 * Renvoie la liste des <code>products</code>, avec l'<code>id</code> et le <code>name</code> de la <code>category</code> auxquels ils appartiennent
	 */
	public function fetchAllProducts()
	{
		return $this->_db->select()
			->from( array( 'p' => 'products' ), array( 'id', 'name', 'poids' ) )
			->join( array( 'c' => 'categories' ), 'p.xcategory = c.id', array( 'category' => 'name', 'xcategory' => 'id' ) )
			->join( array( 'u' => 'units' ), 'u.id = p.xunit', array( 'xunit' => 'id', 'unit' => 'name' ) )
			->order( 'c.name ASC' )
			->order( 'p.name ASC' )
			->query();
	}
	
	
	public function fetchProduct( $id )
	{
		if( $id == null || empty( $id ) )
			throw new Zend_Db_Table_Exception( "Too few argument for the primary key" );
		
		return $this->_db->select()
			->from( array( 'p' => 'products' ), array( 'id', 'name', 'poids' ) )
			->join( array( 'c' => 'categories' ), 'p.xcategory = c.id', array( 'category' => 'name', 'xcategory' => 'id' ) )
			->join( array( 'u' => 'units' ), 'u.id = p.xunit', array( 'xunit' => 'id', 'unit' => 'name' ) )
			->where( 'p.id = ?', $id )
			->order( 'c.name ASC' )
			->order( 'p.name ASC' )
			->query();
	}
}

