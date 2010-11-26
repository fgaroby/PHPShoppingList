<?php

/**
 * UnitController
 * 
 * @author
 * @version 
 */


class UnitController extends Jcaddie_Controller_Abstract
{


	/**
	 * 
	 * Table des unités
	 * @var Zend_Db_Table_Abstract _unitTable
	 */
	private $_unitTable;


	public function init()
	{
		parent::init();
		
		$this->_unitTable = new Application_Model_DbTable_Unit();
		
		// par défaut un appel à render() annule le rendu automatique
		// restauration du rendu via le helper viewRenderer.
		// (cette action rend une vue)
		$this->_helper->viewRenderer->setNoRender( false );
	
	}


	/**
	 * The default action - show the home page
	 */
	public function indexAction()
	{
		$this->view->setTitrePage( 'Liste des unités' );
		
		$this->view->entries = $this->_unitTable->fetchAll();
		
		$this->render();
	}
	
	
	public function editAction()
	{
		$params = $this->getRequest()->getParams();
		$isUpdate = isset( $params['id'] ) && !empty( $params['id'] );
		if( $isUpdate )
		{
			// Vérification des droits
			//$this->_helper->aclCheck( $params['id'], 'editer' );
			$unit = $this->_unitTable->find( $params['id'] )->current();
			$this->view->setTitrePage( 'Éditer l\'unité : <i>"' . $unit->name . '"</i>' );
		}
		else
		{
			//$this->_helper->aclCheck( 'produits', 'ajouter' );
			$this->view->setTitrePage( 'Ajouter une unité' );
			
			// Création d'un produit vide s'il s'agit d'un ajout
			$unit = $this->_unitTable->createRow();
		}
		
		// Création du formulaire et déclaration des paramètres généraux
		$form = new Jcaddie_Form_Unit();
		$form->setAction( $this->view->link( 'unit', 'edit', null, '', 'default', ! $isUpdate ) )
			->setMethod( 'post' )
			->setDefaults( $unit->toArray() );
		
		// création du formulaire et ajout/suppression
		if( $this->getRequest()->isPost() && $form->isValid( $_POST ) )
		{
			// Retrait des informations depuis les données en POST et ajout dans le modèle
			$values = $form->getValues();
			

			$unit->setFromArray( array_intersect_key( $values, $unit->toArray() ) );
			
			// Sauvegarde des informations
			$unit->save();
			
			// Sauvegarde des ACL concernant ce produit
			/*if( ! $isUpdate )
				$this->_helper->aclCheck->acl->add( new Zend_Acl_Resource( $product->id ) );*/
			
			//$this->_helper->aclCheck->acl->allow( 'user', $product->id );
			


			// Suppression du cache pour mise à jour
			Jcaddie_Cache::clean( 'products' );
			
			// Redirection vers la liste des produits
			$this->_redirect( $this->view->url( array(), 'unit' ), array( 'prependBase' => false ) );
		}
		
		// Assignation du formulaire pour affichage
		$this->view->form = $form;
	}

}
