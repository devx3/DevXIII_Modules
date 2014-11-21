<?php

class DevXIII_CpfPlus_Model_Observer
{
	
	public function convertPassengers( $observer )
	{
		
		$item = $observer->getQuoteItem();
		$item->setPassengersData('Teste'); 
		$item->save();
		
		return $this;
		
		$product = $observer->getProduct();
		
		$params = $observer->getRequest()->getParams();
		
		if( $params['passengers_data'] ):
			Mage::getSingleton('core/session')->setPassengersData(serialize($params['passengers_data']));
		endif;
		
		print_r(Mage::getSingleton('core/session')->getPassengersData()); die;
		return $this;
		
	}
	
}
