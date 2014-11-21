<?php

class DevXIII_CpfPlus_Model_Observer
{
	
	public function convertPassengers( $observer )
	{
		
		$item 	= $observer->getQuoteItem();
		$params = $_POST;

		if( $params['passengers_data'] ):
			$item->setPassengersData( serialize($params['passengers_data']) );
		endif;
		return $this;
		
	}
	
}
