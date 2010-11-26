<?php
/**
 *
 * @author Francescu Garoby
 * @version 
 */

/**
 * RedirectorToOrigin Action Helper 
 * 
 * @uses actionHelper Jcaddie_Controller_ActionHelpers
 */
class Jcaddie_Controller_ActionHelpers_RedirectorToOrigin extends Zend_Controller_Action_Helper_Abstract
{


	/**
	 * Strategy pattern: call helper as broker method
	 */
	public function direct( $message = null )
	{
		// Insertion du message dans le flash messenger
		if( ! is_null( $message ) )
			Zend_Controller_Action_HelperBroker::getStaticHelper( 'FlashMessenger' )->addMessage( $message );
		

		// Redirection
		if( ! isset( Zend_Registry::get( 'session' )->requestUri ) )
			$gotoUrl = $this->frontController()->getBaseUrl();
		else
			$gotoUrl = Zend_Registry::get( 'session' )->requestUri;
		
		Zend_Controller_Action_HelperBroker::getStaticHelper( 'redirector' )->setCode( 303 )->gotoUrl( $gotoUrl, array( 'prependBase' => false ) );
	}


	public function setFlashMessengerNamespace( $namespace )
	{
		Zend_Controller_Action_HelperBroker::getStaticHelper( 'FlashMessenger' )->setNamespace( $namespace );
		
		return $this;
	}
}
