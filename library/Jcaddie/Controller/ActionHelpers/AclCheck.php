<?php
/**
 * Aide d'action permettant l'export en PDF de collection de réservations
 * 
 * @package jcaddie
 * @subpackage controller
 */
class Jcaddie_Controller_ActionHelpers_AclCheck extends Zend_Controller_Action_Helper_Abstract
{


	/**
	 * @var Zend_Acl
	 */
	public $acl;


	/**
	 * Initialisation de l'aide (à considérer comme un constructeur )
	 */
	public function init()
	{
		$this->acl = Zend_Registry::get( 'session' )->acl;
	}


	/**
	 * Pattern Strategy
	 * 
	 * @param TReservation $reservations
	 * @param string $format (pdf, csv, ...)
	 */
	public function direct( $reservation, $accessType = null )
	{
		/* essai de l'ACL, si la room n'y est pas pour une raison X
           alors une exception sera levée */
		/*try
		{
			$aclResult = $this->acl->isAllowed( 'user', $reservation, $accessType );
		}
		catch( Zend_Acl_Exception $e )
		{
			$aclResult = false;
		}
		if( ! Zend_Auth::getInstance()->hasIdentity() || ! $aclResult )
		{
			Zend_Controller_Action_HelperBroker::getStaticHelper( 'redirector' )->gotoUrl( '/unauthorized' );
		}*/
	}
}