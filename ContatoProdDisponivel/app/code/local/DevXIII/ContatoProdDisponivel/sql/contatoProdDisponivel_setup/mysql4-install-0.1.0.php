<?php

/**
 * Create The Custom attribute to catalog_products
 * @category DevXIII
 * @package DevXIII_ContatoProdDisponivel
 * @copyright Copyright (c) 2014 Erick Bruno
 * @author Erick Bruno <erickfabiani123@gmail.com>
 *  
 */

$attribute = array(
	'group'    => 'Prices',
	'type'     => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
	'backend'  => '',
	'frontend' => '',
	'label'    => 'Sob Consulta?',
	'input'    => 'boolean',
	'class'	   => '',
	'source'   => '',
	'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'visible'  => true,
	'required' => false,
	'user_defined' => true,
	'default'  => 0,
	'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => true,
    'apply_to'          => 'simple,configurable,virtual',
    'is_configurable'   => false
);

$installer = $this;
$installer->startSetup();
$installer->addAttribute('catalog_product', 'sobconsulta', $attribute);
$installer->endSetup();

