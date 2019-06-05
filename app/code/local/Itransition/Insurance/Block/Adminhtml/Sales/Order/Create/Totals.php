<?php

class Itransition_Insurance_Block_Adminhtml_Sales_Order_Create_Totals extends Mage_Adminhtml_Block_Sales_Order_Create_Totals
{
    protected $_template = 'itransition/insurance/sales/order/create/totals/insurance.phtml';

    public function __construct()
    {
        parent::__construct();
        $address = $this->getQuote()->getShippingAddress();
        if ($address->getBaseInsurance()) {
            $this->setBaseInsurance($address->getBaseInsurance());
            $this->setInsurance($address->getInsurance());
            $address->addTotal([
                    'code' => 'insurance',
                    'value' => $address->getBaseInsurance(),
                    'title' => $this->helper('itransition_insurance')->__('Insurance'),
                ]
            );
        }
    }

    public function getLabel()
    {
        return $this->helper('itransition_insurance')->__('Insurance');
    }
}