<?php

class DevXIII_GoogleTracker_Model_Observer
{
	/**
	 * Código do analytics (Ex: UA-XXXXXX-X)
	 */
	protected $_analyticsCode;

	/**
	 * Será montado o código nesse array e depois retornado com "\n" 
	 */
	protected $_data = array();

	public function __construct()
    {
    	
    }

    /**
	 * Pega os dados do pedido e envia para o analytics
	 * @param object $observer Pega o evento
	 * @return string Retorna o código do analytics
	 */
	public function trackingOrder(Varien_Event_Observer $observer)
	{
		// Pega o código do analytics
		$this->_analyticsCode = Mage::getStoreConfig('google/analytics/account');

		// Pega as informações do pedido
		$order 	  = $observer->getOrder();
		$customer = $order->getCustomer();
		$address  = $order->getBillingAddress();
		$grandTotal = $order->getGrandTotal();
		$orderId 	= $order->getRealOrderId();

		// Pega os itens comprados
		$itens = Mage::getSingleton('checkout/session') -> getQuote() -> getAllItems();

		// Monta o código do analytics
		$this->_data[] = $this->_headerAnalytics();
		$this->_data[] = $this->_openJs();

			$this->_data[] = 'var pageTracker = _gat._getTracker("'.$this->_analyticsCode.'"); pageTracker._trackPageview();';

			$this->_data[] = sprintf("pageTracker._addTrans('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');",
				$orderId,
				Mage::getStoreConfig('general/store_information/name'),
				$grandTotal,
				'0.0',
				'0.0',
				'Curitiba',
				'Paraná',
				'Brasil'
			);

			foreach ($itens as $item):
				$this->_data[] = sprintf("pageTracker._addItem('%s','%s','%s','%s','%s','%s');",
					$orderId, 
					$item->getSku(), 
					$item->getName(), 
					null,
					$item->getPrice(), 
					$item->getQty()
				);
			endforeach;
		
		$this->_data[] = "pageTracker._trackTrans();";
		$this->_data[] = $this->_closeJs();

		return implode("\n", $this->_data);

	}

	/**
	 * Abre Tag script
	 */
	protected function _openJs()
	{
		return '<script type="text/javascript" >';
	}

	/**
	 * Fecha Tag script
	 */
	protected function _closeJs()
	{
		return '</script>';
	}

	/**
	 * Seta cabeçalho para o analytics
	 */
	protected function _headerAnalytics()
	{

		return $header=<<<EOF
<script type="text/javascript" charset="utf-8">
	var gaJsHost = (("https:" == document.location.protocol ) ? "https://ssl." : "http://www.");
  	document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
EOF;

	}

}