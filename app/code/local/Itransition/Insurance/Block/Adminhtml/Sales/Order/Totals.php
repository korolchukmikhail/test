<?php

class Itransition_Insurance_Block_Adminhtml_Sales_Order_Totals extends Mage_Adminhtml_Block_Sales_Order_Totals
{
    /**
     * @inheritdoc
     */
    protected function initTotals()
    {
        /** @var Itransition_Insurance_Helper_Data $helper **/
        $helper = Mage::helper('itransition_insurance');
        $helper->initTotals($this);

        return $this;
    }
}