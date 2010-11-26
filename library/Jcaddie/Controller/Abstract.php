<?php

/**
 * AbstractController
 * 
 * @author
 * @version 
 */


abstract class Jcaddie_Controller_Abstract extends Zend_Controller_Action
{
	public function init()
	{
		$controller = $this->getRequest()->getParam( 'controller' );
		$defaultAction = $this->getFrontController()->getDefaultAction();
		
		// Chargement des fichiers CSS
		$this->view->headLink()->prependStylesheet( 'css/table.css' );
		$this->view->headLink()->prependStylesheet( 'css/form.css' );
		
		// Création du sous-menu
		$this->view->submenu = array( $this->view->link( $controller, $defaultAction ) => $this->view->translate( 'Accueil' ), 
		$this->view->link( 'product', 'index' ) => $this->view->translate( 'Consultez les produits' ), 
		$this->view->link( 'category', 'index' ) => $this->view->translate( 'Consultez les catégories' ), 
		$this->view->link( 'enseigne', 'index' ) => $this->view->translate( 'Consultez les enseignes' ), 
		$this->view->link( 'unit', 'index' ) => $this->view->translate( 'Consultez les unités' ), 
		$this->view->link( 'caddie', 'index' ) => $this->view->translate( 'Créez votre caddie' ) );
		// rendu du sous-menu dans le segment 'submenu' de la réponse
		// Le Layout va réagir à ceci.
		$this->renderScript( 'common/submenu.phtml', 'submenu' );
	}
}