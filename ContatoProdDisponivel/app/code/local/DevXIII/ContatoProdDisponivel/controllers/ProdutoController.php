<?php 

class DevXIII_ContatoProdDisponivel_ProdutoController 
	extends Mage_Core_Controller_Front_Action {
	
	public function indexAction()
	{
		
		$data = array(
			'email'   => $this->getRequest()->getParam('email'),
			'assunto' => $this->getRequest()->getParam('assunto'),
			'msg'	  => $this->getRequest()->getParam('mensagem')
		);
		echo json_encode( Mage::getStoreConfig('contatoProdDisponivel_options') );
		
	}

}
