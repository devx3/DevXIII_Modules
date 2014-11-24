<?php

class DevXIII_CpfPlus_Block_Sales_Items_Abstract
	extends Mage_Adminhtml_Block_Sales_items_Abstract
{
	public function getPassengersData( $item )
	{
		$itemId = $item->getId();
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$query = "SELECT 
					q.passengers_data 
					FROM
						sales_flat_order_item o
					LEFT JOIN
						sales_flat_quote_item q
					ON
						o.quote_item_id = q.item_id
					WHERE
						o.item_id =".$itemId;
		$res = $write->query($query);
		$row = $res->fetch();
		return unserialize($row['passengers_data']);
	}
}