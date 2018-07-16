<?php

class Itransition_Insurance_Model_Sales_Creditmemo extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{

    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        if (!Mage::helper('it_insurance')->isEnabled()) {
            return $this;
        }

        $address = $creditmemo->getOrder()->getShippingAddress();
        if ($address->getBaseInsurance()) {
            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $address->getInsurance());
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $address->getBaseInsurance());
        }

        return $this;
    }
}