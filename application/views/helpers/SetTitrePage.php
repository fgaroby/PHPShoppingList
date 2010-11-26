<?php
/**
 *
 * @author windu
 * @version 
 */

/**
 * SetTitrePage helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_SetTitrePage extends Zend_View_Helper_Abstract
{
	const TITRE_PAGE_VAR = 'pageTitle';


	/**
	 * 
	 */
	public function setTitrePage( $titre )
	{
		$this->view->{self::TITRE_PAGE_VAR} = $this->view->translate( $titre );
	}
}
