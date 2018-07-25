<?php

class Itransition_Insurance_Helper_Data extends Mage_Core_Helper_Data
{

    const PAYPAL_ITEM_NAME = 'Insurance';

    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag('insurance/config/enabled', $store);
    }

    public function setInsuranceToAddress($address, $insurance, $save = false)
    {
        if ($insurance) {
            $insurance = (float)$insurance;
            $address->setInsurance(Mage::app()->getStore()->convertPrice($insurance, false));
            $address->setBaseInsurance($insurance);
        } else {
            $address->setInsurance(0);
            $address->setBaseInsurance(0);
        }

        if ($save) {
            $address->save();
        }
    }

    public function addTotal($parentBlock, Mage_Sales_Model_Order_Address $address)
    {
        if ($address->getBaseInsurance()) {
            $parentBlock->addTotal(
                new Varien_Object([
                    'code' => 'insurance',
                    'value' => $address->getInsurance(),
                    'base_value' => $address->getBaseInsurance(),
                    'label' => $this->__('Insurance'),
                ]), 'subtotal');
        }
    }

    public function initTotals($totalsBlock)
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $parentBlock = $totalsBlock->getParentBlock();
        $address = $parentBlock->getOrder()->getShippingAddress();

        $this->addTotal($parentBlock, $address);

        return true;
    }
}