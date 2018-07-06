<?php
/**
 * Created by PhpStorm.
 * User: m.korolchuk
 * Date: 6.7.18
 * Time: 15.09
 */

class Itransition_Insurance_Model_Observer {

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