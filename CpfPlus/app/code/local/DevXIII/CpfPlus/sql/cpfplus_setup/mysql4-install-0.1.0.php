<?php

$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE sales_flat_quote_item 
	ADD passengers_data TEXT
");
$installer->endSetup();
