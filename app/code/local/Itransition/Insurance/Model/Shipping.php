<?php
/**
 * Created by PhpStorm.
 * User: m.korolchuk
 * Date: 5.7.18
 * Time: 18.45
 */

class Itransition_Insurance_Model_Shipping extends Mage_Core_Model_Abstract {

    public function getCarriers() {
        $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();

        $options = [];

        foreach ($methods as $_ccode => $_carrier) {
            $_method_options = [];
            if ($_methods = $_carrier->getAllowedMethods()) {
                foreach ($_methods as $_mcode => $_method) {
                    $_code = $_ccode . '_' . $_mcode;
                    $_method_options[] = ['value' => $_code, 'label' => $_method];
                }

                if (!$_title = Mage::getStoreConfig("carriers/$_ccode/title"))
                    $_title = $_ccode;

                $options[] = ['value' => $_method_options, 'label' => $_title];
            }
        }

        return $options;
    }

    public function getRates() {
        $rates = [];
        if ($rates_source = Mage::getStoreConfig('insurance/config/rates')) {
            $rates_source = unserialize($rates_source);
            foreach ($rates_source['value'] as $i => $method_code) {
                $rates[$method_code] = [
                    'state' => (int)$rates_source['state'][$i],
                    'percent' => (int)$rates_source['percent'][$i],
                    'rate' => (float)$rates_source['rate'][$i]
                ];
            }
        }

        return $rates;
    }
}