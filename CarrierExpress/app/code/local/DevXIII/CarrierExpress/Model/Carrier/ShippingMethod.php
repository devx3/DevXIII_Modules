<?php
/**
 * Erick Bruno Fabiani (aka DevXIII)
 * 
 * @category DevXIII
 * @package DevXIII_CarrierExpress
 * @copyright (c) 2015, Erick Bruno Fabiani
 * @author Erick Bruno Fabiani <erickfabiani123@gmail.com>
 */
class DevXIII_CarrierExpress_Model_Carrier_ShippingMethod extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

    protected $_code = 'devxiii_carrierexpress';
    
    /**
     * Grupos de peso para calculo do valor
     * @var type array
     */
    protected $_groups = array(
        1 => array(
            'ssc' => 29.32,
            'nnc' => 59.04
        ),
        2 => array(
            'ssc' => 63.15,
            'nnc' => 143.05
        ),
        3 => array(
            'ssc' => 85.24,
            'nnc' => 197.20
        ),
        4 => array(
            'ssc' => 231.40,
            'nnc' => 368.12
        ),
        5 => array(
            'ssc' => 328.34,
            'nnc' => 623.42
        ),
        
    );

    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {

        $storeConfig = 'carriers/' . $this->_code . '/';
        $carrierHelper = Mage::helper('devxiii_carrierexpress');
        
        if (!Mage::getStoreConfig($storeConfig . 'active')) {
            return false;
        }
        
        $itemsWeight = 0;
        $exception = 0;
        foreach ($request->getAllItems() as $item) {
            $exception += $carrierHelper->getCarrierException($item->getProductId());
            $itemsWeight += ($item->getWeight() * $item->getQty());
        }
        
        if( $exception == 0 ) {
            $exception = 1; 
        }

        $postcode = (int) str_replace('-', '', trim($request->getDestPostcode()));
        
        $groupId = $this->_returnCarrierGroup($itemsWeight);
        $shippingPrice = $this->_getCarrierPrice($postcode, $groupId);

        if( $shippingPrice === false ){
            return false;
        }
        
        $result = Mage::getModel('shipping/rate_result');

        $method = Mage::getModel('shipping/rate_result_method');
        $method->setCarrier($this->_code);
        $method->setCarrierTitle(Mage::getStoreConfig($storeConfig . 'title'));

        $method->setMethod(Mage::getStoreConfig($storeConfig . 'title'));
        $method->setMethodTitle(Mage::getStoreConfig($storeConfig . 'title'));

        $method->setCost(0);
        $method->setPrice($shippingPrice*$exception);
        $result->append($method);

        return $result;
    }

    /**
     * Retona o grupo do produto
     * @param type $itemsWeight
     * @return int
     */
    protected function _returnCarrierGroup($itemsWeight) {

        switch ($itemsWeight) {
            case ( $itemsWeight > 0 && $itemsWeight <= 30 ):
                return 1;
                break;
            case ( $itemsWeight > 30 && $itemsWeight <= 60 ):
                return 2;
                break;
            case ( $itemsWeight > 60 && $itemsWeight <= 120 ):
                return 3;
                break;
            case ( $itemsWeight > 120 && $itemsWeight <= 300 ):
                return 4;
                break;
            case ( $itemsWeight > 300 && $itemsWeight <= 1000 ):
                return 5;
                break;
            default:
                //multiplica por 30%
                $priceSsc = (328.34 + (328.34 * 0.3));
                $priceNnc = (623.42 + (623.42 * 0.3));
                $this->_groups[6] = array(
                    'ssc' => $priceSsc,
                    'nnc' => $priceNnc,
                );
                return 6;
                
                break;
        }
    }

    public function getAllowedMethods() {
        return array($this->_code => $this->getConfigData('title'));
    }

    /**
     * Retorna o preço com base no peso e no CEP
     * @param type $postcode CEP de destino
     * @param type $groupId GroupId do(s) produto(s)
     * @return boolean|float
     */
    protected function _getCarrierPrice( $postcode, $groupId ) {
        
        switch ($postcode) {
            case ( $postcode >= 40000000 && $postcode < 78900000 ):
                return $this->_groups[$groupId]['nnc'];
                break;
            case ( ($postcode >= 01000000 && $postcode < 40000000) ||
                    $postcode >= 79000000 && $postcode < 100000000 ):
                return $this->_groups[$groupId]['ssc'];
                break;
            default:
                return false;
                break;
        }
    }
}
