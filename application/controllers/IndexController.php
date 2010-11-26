<?php
class IndexController extends Jcaddie_Controller_Abstract
{
	public function indexAction()
	{
		$this->view->setTitrePage( 'Jcaddie :: Accueil' );
	}


	public function infoAction()
	{
		if( $this->getInvokeArg( 'debug' ) == 1 )
		{
			$this->getResponse()->setHeader( 'Cache-control', 'no-cache' );
			$this->view->setTitrePage( 'Contenu de request et response' );
			$this->view->request = $this->getRequest();
			$this->view->response = $this->getResponse();
		}
	}
}
