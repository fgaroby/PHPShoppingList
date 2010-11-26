<?php

/**
 * ProductController
 * 
 * @author
 * @version 
 */


class ProductController extends Jcaddie_Controller_Abstract
{


	/**
	 * 
	 * Table des produits
	 * @var Zend_Db_Table_Abstract _productTable
	 */
	private $_productTable;


	public function init()
	{
		parent::init();
		
		$this->_productTable = new Application_Model_DbTable_Product();
		
		// par défaut un appel à render() annule le rendu automatique
		// restauration du rendu via le helper viewRenderer.
		// (cette action rend une vue)
		$this->_helper->viewRenderer->setNoRender( false );
	
	}


	/**
	 * The default action - show the products table
	 */
	public function indexAction()
	{
		$this->view->setTitrePage( 'Liste des produits' );
		
		$this->view->entries = $this->_productTable->fetchAllProducts();
		
		$this->render();
	}


	/**
	 * 
	 * Edite et enregistre un produit
	 */
	public function editAction()
	{
		$params = $this->getRequest()->getParams();
		$isUpdate = isset( $params['id'] ) && ! empty( $params['id'] );
		if( $isUpdate )
		{
			// Vérification des droits
			//$this->_helper->aclCheck( $params['id'], 'editer' );
			$product = $this->_productTable->find( $params['id'] )->current();
			$this->view->setTitrePage( 'Éditer le produit : <i>"' . $product->name . '"</i>' );
		}
		else
		{
			//$this->_helper->aclCheck( 'produits', 'ajouter' );
			$this->view->setTitrePage( 'Ajouter un produit' );
			
			// Création d'un produit vide s'il s'agit d'un ajout
			$product = $this->_productTable->createRow();
		}
		
		// Création du formulaire et déclaration des paramètres généraux
		$form = new Jcaddie_Form_Product();
		//$form->addDecorator( "HtmlTag", array( "tag" => "span" ) );
		$form->setAction( $this->view->link( 'product', 'edit', null, '', 'default', ! $isUpdate ) )
			->setMethod( 'post' )
			->setDefaults( $product->toArray() );
		
		// création du formulaire et ajout/suppression
		if( $this->getRequest()->isPost() && $form->isValid( $_POST ) )
		{
			// Retrait des informations depuis les données en POST et ajout dans le modèle
			$values = $form->getValues();
			

			$product->setFromArray( array_intersect_key( $values, $product->toArray() ) );
			
			// Sauvegarde des informations
			$product->save();
			
			// Sauvegarde des ACL concernant ce produit
			/*if( ! $isUpdate )
				$this->_helper->aclCheck->acl->add( new Zend_Acl_Resource( $product->id ) );*/
			
			//$this->_helper->aclCheck->acl->allow( 'user', $product->id );
			


			// Suppression du cache pour mise à jour
			Jcaddie_Cache::clean( 'products' );
			
			// Redirection vers la liste des produits
			$this->_redirect( $this->view->url( array(), 'product' ), array( 'prependBase' => false ) );
		}
		
		// Assignation du formulaire pour affichage
		$this->view->form = $form;
	}


	public function consultAction()
	{
		$params = $this->getRequest()->getParams();
		if( ! isset( $params['id'] ) || empty( $params['id'] ) )
			// Redirection vers la liste des produits
			$this->_redirect( $this->view->url( array(), 'product' ), array( 'prependBase' => false ) );
		else
		{
			$id = $params['id'];
			$product = $this->_productTable->fetchProduct( $id );
			$this->view->setTitrePage( 'Consulter le produit : <i>"' . $product->name . '"</i>' );
			$this->view->product = $product;
			
			$selects = new Application_Model_DbTable_Shop();
			$this->view->magasins = $selects->fetchAll();
		}
	}
}
