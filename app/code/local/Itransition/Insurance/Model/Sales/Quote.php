<?php
/**
 * Created by PhpStorm.
 * User: m.korolchuk
 * Date: 9.7.18
 * Time: 14.23
 */

class Itransition_Insurance_Model_Sales_Quote extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    public function __construct() {
        $this->setCode('insurance');
    }

    public function getLabel() {
        return Mage::helper('it_insurance')->__('Insurance');
    }

    public function collect(Mage_Sales_Model_Quote_Address $address) {
        parent::collect($address);
        if (($address->getAddressType() == 'billing')) {
            return $this;
        }

        $amount = $address->getInsurance();

        if ((float)$amount > 0) {
            $address->setInsuranceAmount($amount);
            $address->setBaseInsuranceAmount($amount);

            $address->setGrandTotal($address->getGrandTotal() + $address->getInsuranceAmount());
            $address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseInsuranceAmount());
        }

        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address) {
        if (($address->getAddressType() == 'shipping')) {
            $amount = $address->getInsurance();

            if ((float)$amount > 0) {
                $address->addTotal(array(
                    'code' => $this->getCode(),
                    'title' => $this->getLabel(),
                    'value' => $amount
                ));
            }
        }

        return $this;
    }
}