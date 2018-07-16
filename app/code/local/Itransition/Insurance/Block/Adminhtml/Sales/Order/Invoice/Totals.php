<?php

class Itransition_Insurance_Block_Adminhtml_Sales_Order_Invoice_Totals extends Mage_Adminhtml_Block_Sales_Order_Invoice_Totals
{
    /**
     * Initialize order totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        if (!Mage::helper('it_insurance')->isEnabled()) {
            return $this;
        }

        $address = $this->getSource()->getShippingAddress();
        if ($address->getBaseInsurance()) {
            $this->addTotalBefore(
                new Varien_Object([
                    'code' => 'insurance',
                    'value' => $address->getInsurance(),
                    'base_value' => $address->getBaseInsurance(),
                    'label' => $this->helper('it_insurance')->__('Insurance'),
                ]), ['shipping', 'tax']);
        }

        return $this;
    }
}