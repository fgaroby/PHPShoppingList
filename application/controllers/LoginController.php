<?php

/**
 * LoginController
 * 
 * @author windu
 * @version 
 */

require_once 'Zend/Controller/Action.php';

class LoginController extends Zend_Controller_Action
{


	/**
	 * Instance de Zend_Auth
	 * 
	 * @var Zend_Auth
	 */
	private $_auth;


	/**
	 * Appelé à la construction : instancie Zend Auth
	 */
	public function init()
	{
		$this->_auth = Zend_Auth::getInstance();
	}


	/**
	 * Identification
	 */
	public function loginAction()
	{
		// Attribution du namespace dans le flashmessenger pour le message d'erreur éventuel
		$this->_helper->redirectorToOrigin->setFlashMessengerNamespace( 'loginForm' );
		

		if( ! $this->_request->isPost() || ! $this->_request->getPost( 'login' ) || ! $this->_request->getPost( 'password' ) )
		{
			$this->_helper->redirectorToOrigin( 'veuillez entrer un login ou mot de passe' );
		}
		// création de l'authentificateur
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$dbAdapter = new Zend_Auth_Adapter_DbTable( $db, 'user', 'email', 'password', 'MD5(?)' );
		
		// création des validateurs
		$validator = new Zend_Validate();
		$validator->addValidator( new Zend_Validate_NotEmpty() );
		$validator->addValidator( new Zend_Validate_StringLength( 4 ) );
		$validatorLogin = clone $validator;
		$validatorLogin->addValidator( new Zend_Validate_EmailAddress() );
		
		// validation des paramètres d'entrée
		if( ! $validatorLogin->isValid( $this->_request->getPost( 'login' ) ) || ! $validator->isValid( $this->_request->getPost( 'password' ) ) )
		{
			$this->_helper->redirectorToOrigin( 'login ou mot de passe incorrect' );
		}
		
		// passage des paramètres à l'authentificateur
		$dbAdapter->setCredential( $this->_request->getPost( 'password' ) )
			->setIdentity( $this->_request->getPost( 'login' ) );
		
		// authentification          
		$result = $this->_auth->authenticate( $dbAdapter );
		
		// écriture de l'objet complet en session, sauf le champ password,
		// si l'identification est OK
		if( $result->isValid() )
		{
			$this->_auth->getStorage()->write( $res = $dbAdapter->getResultRowObject( null, 'password' ) );
			
			// Montage des acls
			$this->_setAcls( $res );
			
			// regénération de l'id de session (évite les fixations de session)
			Zend_Session::regenerateId();
			$this->_redirect( '/' );
		}
		else
		{
			$this->_helper->redirectorToOrigin( 'login ou mot de passe incorrect' );
		}
	
	}


	/**
	 * Affiche le formulaire de login ou le message de bienvenue
	 */
	public function indexAction()
	{
		if( $this->_auth->hasIdentity() )
		{
			$this->view->login = $this->_auth->getIdentity()->firstname;
			$this->render( 'welcome' );
		}
		else
		{
			$this->render( 'loginform' );
		}
	}


	/**
	 * Mise en place des ACL
	 * Méthode appelée par loginAction en début de session utilisateur
	 * 
	 * @param stdClass $user
	 */
	private function _setAcls( stdClass $user )
	{
		/*$TReservation = new TReservation();
		
		// récupération de toutes les reservations
		$reservations = $TReservation->fetchAll( $TReservation->select()
			->from( $TReservation, 'id' ) )
			->toArray();
		
		// récupération des acls depuis la session
		$acl = Zend_Registry::get( 'session' )->acl;
		foreach( $reservations as $reservation )
		{
			// ajout des reservations existantes dans les acls
			$acl->add( new Zend_Acl_Resource( $reservation['id'] ) );
		}
		if( $user->is_admin == 1 )
		{
			// l'admin à tous les droits
			$acl->allow( 'user' );
		}
		else
		{
			// récupération des reservations dont l'utilisateur est créateur
			$reservationsOwned = $TReservation->getByCreator( $user->id );
			foreach( $reservationsOwned as $reservationOwned )
			{
				// autorisation d'accès sur ces reservations pour cet utilisateur
				$acl->allow( 'user', $reservationOwned['id'], 'editer' );
			}
			// autorisation d'ajouter des reservations limitée par une assertion
			$acl->allow( 'user', 'reservations', 'ajouter' );
		}*/
	}


	/**
	 * Déconnexion de l'utilisateur
	 * La session est totalement détruite afin de détruire aussi les acls
	 */
	public function logoutAction()
	{
		Zend_Session::destroy();
		$this->_redirect( '/' );
	}
}
