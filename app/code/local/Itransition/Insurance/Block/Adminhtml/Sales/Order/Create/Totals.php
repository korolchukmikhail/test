<?php

class Itransition_Insurance_Block_Adminhtml_Sales_Order_Create_Totals extends Mage_Adminhtml_Block_Sales_Order_Create_Totals
{
    protected $_template = 'itransition/sales/order/total.phtml';

    public function __construct()
    {
        parent::__construct();
        $address = $this->getQuote()->getShippingAddress();
        if ($address->getBaseInsurance()) {
            $this->addTotal(
                new Varien_Object([
                    'code' => 'insurance',
                    'value' => $address->getInsurance(),
                    'base_value' => $address->getBaseInsurance(),
                    'label' => $this->helper('itransition_insurance')->__('Insurance'),
                ]), ['shipping', 'tax']);
        }
    }
}