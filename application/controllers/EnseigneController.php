<?php

/**
 * EnseigneController
 * 
 * @author
 * @version 
 */

class EnseigneController extends Jcaddie_Controller_Abstract
{


	/**
	 * 
	 * Table des enseignes
	 * @var Zend_Db_Table_Abstract _enseigneTable
	 */
	private $_enseigneTable;


	public function init()
	{
		parent::init();
		
		$this->_enseigneTable = new Application_Model_DbTable_Enseigne();
		
		// par défaut un appel à render() annule le rendu automatique
		// restauration du rendu via le helper viewRenderer.
		// (cette action rend une vue)
		$this->_helper->viewRenderer->setNoRender( false );
	}


	/**
	 * The default action - Liste toutes les <code>enseigne</code>
	 */
	public function indexAction()
	{
		$this->view->setTitrePage( 'Liste des enseignes' );
		
		$this->view->entries = $this->_enseigneTable->fetchAll();
		
		$this->render();
	}


	/**
	 * 
	 * Liste tous les <code>products</code> appartenant à la <code>enseigne</code> dont l'<code>id</code> est passé en paramètre
	 */
	public function listAction()
	{
		
		$params = $this->getRequest()->getParams();

		// Redirection vers la liste des catégories
		if( !isset( $params['id'] ) )
			$this->_redirect( $this->view->url( array(), 'enseigne' ), array( 'prependBase' => false ) );
		
		$this->view->setTitrePage( 'Liste des produits appartenant à la catégorie : <i>"' . $this->_enseigneTable->find( $params['id'] )->current()->name . '"</i>' );
		$this->view->entries = $this->_enseigneTable->fetchAllProductsFromEnseigne( $params['id'] );
	}


	/**
	 * 
	 * Edite et enregistre une catégorie
	 */
	public function editAction()
	{
		$params = $this->getRequest()->getParams();
		$isUpdate = isset( $params['id'] ) && !empty( $params['id'] );
		if( $isUpdate )
		{
			// Vérification des droits
			//$this->_helper->aclCheck( $params['id'], 'editer' );
			$categorie = $this->_enseigneTable->find( $params['id'] )->current();
			$this->view->setTitrePage( 'Éditer la catégorie : <i>"' . $categorie->name . '"</i>' );
		}
		else
		{
			//$this->_helper->aclCheck( 'produits', 'ajouter' );
			$this->view->setTitrePage( 'Ajouter une enseigne' );
			
			// Création d'un produit vide s'il s'agit d'un ajout
			$categorie = $this->_enseigneTable->createRow();
		}
		
		// Création du formulaire et déclaration des paramètres généraux
		$form = new Jcaddie_Form_Enseigne();
		$form->setAction( $this->view->link( 'enseigne', 'edit', null, '', 'default', ! $isUpdate ) )
			->setMethod( 'post' )
			->setDefaults( $categorie->toArray() );
		
		// création du formulaire et ajout/suppression
		if( $this->getRequest()->isPost() && $form->isValid( $_POST ) )
		{
			// Retrait des informations depuis les données en POST et ajout dans le modèle
			$values = $form->getValues();
			
			$categorie->setFromArray( array_intersect_key( $values, $categorie->toArray() ) );
			//die( Zend_Debug::dump($categorie));
			// Sauvegarde des informations
			$categorie->save();
			
			// Sauvegarde des ACL concernant ce produit
			/*if( ! $isUpdate )
				$this->_helper->aclCheck->acl->add( new Zend_Acl_Resource( $product->id ) );*/
			
			//$this->_helper->aclCheck->acl->allow( 'user', $product->id );
			


			// Suppression du cache pour mise à jour
			Jcaddie_Cache::clean( 'categories' );
			
			// Redirection vers la liste des catégories
			$this->_redirect( $this->view->url( array(), 'enseigne' ), array( 'prependBase' => false ) );
		}
		
		// Assignation du formulaire pour affichage
		$this->view->form = $form;
	}
}
