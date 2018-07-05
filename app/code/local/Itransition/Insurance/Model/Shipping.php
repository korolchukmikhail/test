<?php
/**
 * Created by PhpStorm.
 * User: m.korolchuk
 * Date: 5.7.18
 * Time: 18.45
 */

class Itransition_Insurance_Model_Shipping extends Mage_Core_Model_Abstract {

    public function getActiveMethods() {
        $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();

        $options = array();

        foreach ($methods as $_ccode => $_carrier) {
            $_methodOptions = array();
            if ($_methods = $_carrier->getAllowedMethods()) {
                foreach ($_methods as $_mcode => $_method) {
                    $_code = $_ccode . '_' . $_mcode;
                    $_methodOptions[] = ['value' => $_code, 'label' => $_method];
                }

                if (!$_title = Mage::getStoreConfig("carriers/$_ccode/title"))
                    $_title = $_ccode;

                $options[] = ['value' => $_methodOptions, 'label' => $_title];
            }
        }

        return $options;
    }
}