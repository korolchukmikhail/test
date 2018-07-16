<?php

class Itransition_Insurance_Model_Sales_Quote extends Mage_Sales_Model_Quote_Address_Total_Abstract
{

    public function __construct()
    {
        $this->setCode('insurance');
    }

    public function getLabel()
    {
        return Mage::helper('it_insurance')->__('Insurance');
    }

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        if (!Mage::helper('it_insurance')->isEnabled()) {
            return $this;
        }

        if (($address->getAddressType() == 'billing')) {
            return $this;
        }

        $amount = $address->getBaseInsurance();

        if ((float)$amount > 0) {
            $address->setInsuranceAmount($address->getQuote()->getStore()->convertPrice($amount, false));
            $address->setBaseInsuranceAmount($amount);

            $address->setGrandTotal($address->getGrandTotal() + $address->getInsuranceAmount());
            $address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseInsuranceAmount());
        }

        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        if (!Mage::helper('it_insurance')->isEnabled()) {
            return $this;
        }

        if (($address->getAddressType() == 'shipping')) {
            $amount = $address->getBaseInsurance();

            if ((float)$amount > 0) {
                $address->addTotal([
                    'code' => $this->getCode(),
                    'title' => $this->getLabel(),
                    'base_value' => $amount,
                    'value' => $address->getQuote()->getStore()->convertPrice($amount, false),
                ]);
            }
        }

        return $this;
    }
}