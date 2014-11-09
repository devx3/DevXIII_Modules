<?php 

include( 'includes/PHPMailer/class.phpmailer.php' );

class DevXIII_ContatoProdDisponivel_ProdutoController 
	extends Mage_Core_Controller_Front_Action {
	
	/**
	 * Var para armazenar dados de configuração do smtp
	 */
	protected $_configsmtp;
	
	/**
	 * Var para instância da classe PHPMailer
	 */
	protected $_phpMailer;
	
	/**
	 * Configurações da mensagem
	 */
	protected $_messageConfig;
	
	/**
	 * Var com os dados recebidos do formulário
	 */
	protected $_data;
	
	/**
	 * Variáveis que serão substituidas por seus respectivos valores
	 * 
	 * %n = Nome do usuário
	 * %e = Email do usuário
	 * %p = Nome do Produto
	 * %a = Assunto digitado no Form
	 * %m = Mensagem digitada no Form
	 * 
	 */
	protected $_vars  = array( '%n', '%e', '%p', '%a', '%m' );
	
	public function indexAction()
	{
		
		/**
		 * Pegando os dados da Mensagem enviada
		 */
		$this->data = array(
			$this->getRequest()->getParam('nome'),
			$this->getRequest()->getParam('email'),
			$this->getRequest()->getParam('produto'),
			$this->getRequest()->getParam('assunto'),
			$this->getRequest()->getParam('mensagem'),
		);

		/**
		 * Pegando os dados smtp
		 */
		$this->_configsmtp = new stdClass;
		$this->_configsmtp->FromEmail  = Mage::getStoreConfig('contatoProdDisponivel_options/general/from_email');
		$this->_configsmtp->FromName   = Mage::getStoreConfig('contatoProdDisponivel_options/general/from_name');
		$this->_configsmtp->Hostname   = Mage::getStoreConfig('contatoProdDisponivel_options/general/hostname');
		$this->_configsmtp->FromPass   = Mage::getStoreConfig('contatoProdDisponivel_options/general/from_pass');
		$this->_configsmtp->SmtpPort   = Mage::getStoreConfig('contatoProdDisponivel_options/general/smtp_port');
		$this->_configsmtp->Recipients = Mage::getStoreConfig('contatoProdDisponivel_options/general/recipients');
		$this->_configsmtp->Cc 		   = Mage::getStoreConfig('contatoProdDisponivel_options/general/cc');
		$this->_configsmtp->Bcc 	   = Mage::getStoreConfig('contatoProdDisponivel_options/general/bcc');
		
		/**
		 * Pegando dados de configuração da mensagem
		 */
		$tempSubject = Mage::getStoreConfig('contatoProdDisponivel_options/email_config/temp_subject');
		$tempMessage = Mage::getStoreConfig('contatoProdDisponivel_options/email_config/temp_message');
		$this->_configsmtp->TempSubject = str_replace($this->_vars, $this->data, $tempSubject);
		$this->_configsmtp->TempMessage = str_replace($this->_vars, $this->data, $tempMessage); 		 

		echo json_encode( $this->prepareEmailConfig() );
		
	}
	 
	private function prepareEmailConfig()
	{
		if( '' != $this->data )
		{
			
			$this->phpMailer = new PHPMailer;
			$this->phpMailer->IsSMTP();
			$this->phpMailer->SMTPAuth   = true;
			$this->phpMailer->Host 	     = $this->_configsmtp->Hostname;
			$this->phpMailer->Username   = $this->_configsmtp->FromEmail;
			$this->phpMailer->Password   = $this->_configsmtp->FromPass;
			//$this->phpMailer->SMTPSecure = 'tls';
			$this->phpMailer->Port 		 = $this->_configsmtp->SmtpPort;
			
			return $this->prepareEmails()->send();
			
		}
		
	}
	
	private function prepareEmails()
	{
		
		$this->phpMailer->From	     = $this->_configsmtp->FromEmail;
		$this->phpMailer->FromName   = $this->_configsmtp->FromName;
		
		$this->phpMailer->AddAddress( $this->_configsmtp->Recipients );
		$this->phpMailer->AddCC($this->_configsmtp->Cc );
		$this->phpMailer->AddBCC($this->_configsmtp->Bcc);
		
		$this->phpMailer->IsHTML();
		$this->phpMailer->CharSet = 'utf-8';
		$this->phpMailer->Subject = $this->_configsmtp->TempSubject;
		$this->phpMailer->Body 	  = $this->_configsmtp->TempMessage;
		
		return $this;
		
	}
	
	private function send()
	{
		
		//return $this->phpMailer;
		
		if( !$this->phpMailer->Send() )
		{
			return 'Mailer Info: '.$this->phpMailer->ErrorInfo;	
		}
		else
		{
			return 'Mensagem Enviada!';
		}
	}
	
	
}




