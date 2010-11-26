<?php

/**
 * CaddieController
 * 
 * @author
 * @version 
 */


class CaddieController extends Jcaddie_Controller_Abstract
{


	/**
	 * 
	 * Table des catégories
	 * @var Zend_Db_Table_Abstract _caddieTable
	 */
	private $_caddieTable;


	public function init()
	{
		parent::init();
		
		$this->_caddieTable = new Application_Model_DbTable_Caddie();
		
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
		$this->view->setTitrePage( 'Liste des produits dans votre caddie' );
		
		$this->view->entries = $this->_caddieTable->fetchAllProductsFromCaddie();
		
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
			$caddie = $this->_caddieTable->find( $params['id'] )->current();
			$this->view->setTitrePage( 'Éditer le caddie : <i>"' . $caddie->name . '"</i>' );
		}
		else
		{
			//$this->_helper->aclCheck( 'produits', 'ajouter' );
			$this->view->setTitrePage( 'Créer un caddie' );
			
			// Création d'un produit vide s'il s'agit d'un ajout
			$caddie = $this->_caddieTable->createRow();
		}
		
		// Création du formulaire et déclaration des paramètres généraux
		$form = new Jcaddie_Form_Caddie();
		//$form->addDecorator( "HtmlTag", array( "tag" => "span" ) );
		$form->setAction( $this->view->link( 'caddie', 'edit', null, '', 'default', ! $isUpdate ) )
			->setMethod( 'post' )
			->setDefaults( $caddie->toArray() );
		
		// création du formulaire et ajout/suppression
		if( $this->getRequest()->isPost() && $form->isValid( $_POST ) )
		{
			// Retrait des informations depuis les données en POST et ajout dans le modèle
			$values = $form->getValues();
			

			$caddie->setFromArray( array_intersect_key( $values, $caddie->toArray() ) );
			
			// Sauvegarde des informations
			$caddie->save();
			
			// Sauvegarde des ACL concernant ce produit
			/*if( ! $isUpdate )
				$this->_helper->aclCheck->acl->add( new Zend_Acl_Resource( $caddie->id ) );*/
			
			//$this->_helper->aclCheck->acl->allow( 'user', $caddie->id );
			


			// Suppression du cache pour mise à jour
			Jcaddie_Cache::clean( 'caddies' );
			
			// Redirection vers la liste des produits
			$this->_redirect( $this->view->url( array(), 'caddie' ), array( 'prependBase' => false ) );
		}
		
		// Assignation du formulaire pour affichage
		$this->view->form = $form;
	}
}
