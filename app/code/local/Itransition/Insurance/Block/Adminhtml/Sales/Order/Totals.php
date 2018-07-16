<?php

class Itransition_Insurance_Block_Adminhtml_Sales_Order_Totals extends Mage_Adminhtml_Block_Sales_Order_Totals
{
    public function __construct(array $args = [])
    {
        parent::__construct($args);
    }

    /**
     * @inheritdoc
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        if (!Mage::helper('itransition_insurance')->isEnabled()) {
            return $this;
        }

        $address = $this->getSource()->getShippingAddress();
        if ($address->getBaseInsurance()) {
            $this->addTotal(
                new Varien_Object([
                    'code' => 'insurance',
                    'value' => $address->getInsurance(),
                    'base_value' => $address->getBaseInsurance(),
                    'label' => $this->helper('itransition_insurance')->__('Insurance'),
                ]), ['shipping', 'tax']);
        }

        return $this;
    }
}