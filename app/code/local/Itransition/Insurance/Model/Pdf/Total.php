<?php

class Itransition_Insurance_Model_Pdf_Total extends Mage_Sales_Model_Order_Pdf_Total_Default
{
    protected $_insurance = null;

    public function getTotalsForDisplay()
    {
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $totals = [
            [
                'label' => Mage::helper('itransition_insurance')->__(Itransition_Insurance_Helper_Data::MAIN_LABEL_INSURANCE) . ':',
                'amount' => $this->getOrder()->formatPriceTxt($this->getAmount()),
                'font_size' => $fontSize,
            ],
        ];

        return $totals;
    }

    public function getAmount()
    {
        return $this->getInsurance();
    }

    protected function getInsurance()
    {
        if (is_null($this->_insurance)) {
            $order = $this->getOrder();
            $this->_insurance = (float)$order->getShippingAddress()->getInsurance();
        }

        return $this->_insurance;
    }

}