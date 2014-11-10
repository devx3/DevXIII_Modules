<?php 
/**
 * DevXIII
 * 
 * Módulo para contato sobre o produto 
 * 
 * 
 * @author Erick Bruno <erickfabiani123@gmail.com>
 * @package DevXIII_ContatoProdDisponivel
 * @version 0.1.0
 * @copyright Copyright 2014, DevXIII
 * 
 * 
 */
include( 'includes/PHPMailer/class.phpmailer.php' );

class DevXIII_ContatoProdDisponivel_ProdutoController 
	extends Mage_Core_Controller_Front_Action {
	
	/**
	 * Var para armazenar dados de configuração do smtp
	 */
	protected $_configSmtp;
	
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
		$this->_configSmtp = new stdClass;
		$this->_configSmtp->FromEmail  = trim(Mage::getStoreConfig('contatoProdDisponivel_options/general/from_email'));
		$this->_configSmtp->FromName   = trim(Mage::getStoreConfig('contatoProdDisponivel_options/general/from_name'));
		$this->_configSmtp->Hostname   = trim(Mage::getStoreConfig('contatoProdDisponivel_options/general/hostname'));
		$this->_configSmtp->FromPass   = trim(Mage::getStoreConfig('contatoProdDisponivel_options/general/from_pass'));
		$this->_configSmtp->SmtpPort   = trim(Mage::getStoreConfig('contatoProdDisponivel_options/general/smtp_port'));

		
		/**
		 * Pegando dados de configuração da mensagem
		 */
		
		$this->_configSmtp->Recipients = trim(Mage::getStoreConfig('contatoProdDisponivel_options/email_config/recipients'));
		$this->_configSmtp->Cc 		   = trim(Mage::getStoreConfig('contatoProdDisponivel_options/email_config/cc'));
		$this->_configSmtp->Bcc 	   = trim(Mage::getStoreConfig('contatoProdDisponivel_options/email_config/bcc'));
		
		$tempSubject = Mage::getStoreConfig('contatoProdDisponivel_options/email_config/temp_subject');
		$tempMessage = Mage::getStoreConfig('contatoProdDisponivel_options/email_config/temp_message');
		$this->_configSmtp->TempSubject = str_replace($this->_vars, $this->data, $tempSubject);
		$this->_configSmtp->TempMessage = str_replace($this->_vars, $this->data, $tempMessage); 		 

		echo json_encode( $this->prepareEmailConfig() );
		
	}
	 
	private function prepareEmailConfig()
	{
		if( '' != $this->data )
		{
			
			$this->phpMailer = new PHPMailer;
			$this->phpMailer->IsSMTP();
			$this->phpMailer->Host 	     = $this->_configSmtp->Hostname;
			$this->phpMailer->Port 		 = (int) $this->_configSmtp->SmtpPort;
			$this->phpMailer->SMTPAuth   = true;
			$this->phpMailer->Username   = $this->_configSmtp->FromEmail;
			$this->phpMailer->Password   = $this->_configSmtp->FromPass;
			$this->phpMailer->From 	   	 = $this->_configSmtp->FromEmail;
			$this->phpMailer->FromName   = $this->_configSmtp->FromName;
			$this->phpMailer->IsHTML();
			//$this->phpMailer->SMTPSecure = 'tls';
			
			
			return $this->prepareEmails()->send();
			
		}
		
		return 'Error 654: ';
		
	}
	
	private function prepareEmails()
	{
	
		$Addresses = $this->handleEmail( $this->_configSmtp->Recipients );
		$CCs 	   = $this->handleEmail( $this->_configSmtp->Cc );
		$BCCs 	   = $this->handleEmail( $this->_configSmtp->Bcc );
		
		/** ----------------------------------------------
		 * Setando os Destinatários
		 */
		if ( is_array( $Addresses ) ):
			foreach( $Addresses as $Address ):
				$this->phpMailer->AddAddress( $Address );
			endforeach;
		else:
			$this->phpMailer->AddAddress( $Addresses );
		endif;
		
		/** ----------------------------------------------
		 * Setando os emails para cópia
		 */
		if ( is_array( $CCs ) ):
			foreach( $CCs as $CC ):
				$this->phpMailer->AddCC( $CC );
			endforeach;
		else: 
			$this->phpMailer->AddCC( $CCs );
		endif;
		
		/** ----------------------------------------------
		 * Setando os emails para cópia oculta
		 */
		if ( is_array( $BCCs ) ):
			foreach( $BCCs as $BCC ):
				$this->phpMailer->AddBCC( $BCC );
			endforeach;
		else:
			$this->phpMailer->AddBCC( $BCCs );
		endif;

		$this->phpMailer->CharSet = 'utf-8';
		$this->phpMailer->Subject = $this->_configSmtp->TempSubject;
		$this->phpMailer->Body 	  = $this->_configSmtp->TempMessage;
		
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
	
	private function handleEmail( $string )
	{

		if( preg_match('/,/', $string) ):
			
			return explode( ',', trim($string) );
			
		endif;
		return $string;
	}
	
	
}




