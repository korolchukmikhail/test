<?php

class Itransition_Insurance_Helper_Data extends Mage_Core_Helper_Data
{

    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag('insurance/config/enabled', $store);
    }

    public function setInsuranceToAddress($address, $insurance)
    {
        if ($insurance) {
            $insurance = (float)$insurance;
            $address->setInsurance(Mage::app()->getStore()->convertPrice($insurance, false));
            $address->setBaseInsurance($insurance);
        } else {
            $address->setInsurance(0);
            $address->setBaseInsurance(0);
        }
    }
}