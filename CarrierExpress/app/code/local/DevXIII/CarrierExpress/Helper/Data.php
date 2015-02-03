<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class DevXIII_CarrierExpress_Helper_Data 
    extends Mage_Core_Helper_Abstract {
        public function getCarrierException( $productId ){
            
            $products = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('exception_carrierexpress');
            foreach( $products as $product ):
                if( $product->getId() == $productId ):
                    return $product->getData('exception_carrierexpress');
                endif;
            endforeach;
            
        }
    }