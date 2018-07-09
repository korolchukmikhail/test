<?php
/**
 * Created by PhpStorm.
 * User: m.korolchuk
 * Date: 6.7.18
 * Time: 15.09
 */

class Itransition_Insurance_Model_Observer {

    public function unsInsuranceInBilling($observer) {
        $target = $observer->getTarget();
        if($target && $target->getAddressType() && $target->getAddressType() == 'billing'){
            /**
             * Unset new field in billing address for fix null value on OPC with single shipping address
             * This filed is set to NULL
             * app/code/core/Mage/Checkout/Model/Type/Onepage.php 352 line
             */
            $target->unsetData('insurance');
        }
    }

    public function setInsuranceToShippingAddress($observer) {
        $address = $observer->getQuoteAddress();
        if($address->getAddressType() == 'shipping'){
            $request = Mage::app()->getRequest();

            //For cart page, update estimate
            if($insurance = $request->get('shipping_method_insurance')){
                $address->setInsurance($insurance);
                $address->setBaseInsurance($insurance);
            }else{
                $address->setInsurance(0);
                $address->setBaseInsurance(0);
            }
        }
    }

    public function setInsurance($observer) {
        $quote = $observer->getQuote();
        $request = $observer->getRequest();
        $address = $quote->getShippingAddress();

        if ($insurance = $request->getPost('shipping_method_insurance')) {
            $address->setInsurance($insurance);
        }else{
            $address->setInsurance(0);
        }
    }

    public function setMultiShippingInsurance($observer) {
        $quote = $observer->getQuote();
        $request = $observer->getRequest();
        $addresses = $quote->getAllShippingAddresses();

        foreach ($addresses as &$address) {
            if ($insurance = $request->getPost('shipping_method_insurance__' . $address->getId())) {
                $address->setInsurance($insurance);
            }else{
                $address->setInsurance(0);
            }
        }
    }
}