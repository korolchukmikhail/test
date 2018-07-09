<?php
/**
 * Created by PhpStorm.
 * User: m.korolchuk
 * Date: 9.7.18
 * Time: 14.24
 */

class Itransition_Insurance_Model_Sales_Invoice extends Mage_Sales_Model_Order_Invoice_Total_Abstract {

    public function collect(Mage_Sales_Model_Order_Invoice $invoice) {
        $order = $invoice->getOrder();
        $amount = $order->getShippingAddress()->getInsurance();
        if ($amount) {
            $invoice->setGrandTotal($invoice->getGrandTotal() + $amount);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $amount);
        }

        return $this;
    }
}