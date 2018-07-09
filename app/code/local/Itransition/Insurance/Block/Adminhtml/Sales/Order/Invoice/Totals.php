<?php
/**
 * Created by PhpStorm.
 * User: m.korolchuk
 * Date: 9.7.18
 * Time: 14.15
 */

class Itransition_Insurance_Block_Adminhtml_Sales_Order_Invoice_Totals extends Mage_Adminhtml_Block_Sales_Order_Invoice_Totals {
    /**
     * Initialize order totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    protected function _initTotals() {
        parent::_initTotals();
        $amount = $this->getOrder()->getShippingAddress()->getInsurance();
        if ($amount) {
            $this->addTotalBefore(new Varien_Object(array(
                'code' => 'insurance',
                'value' => $amount,
                'base_value' => $amount,
                'label' => $this->helper('it_insurance')->__('Insurance'),
            ), array('shipping', 'tax')));
        }

        return $this;
    }
}