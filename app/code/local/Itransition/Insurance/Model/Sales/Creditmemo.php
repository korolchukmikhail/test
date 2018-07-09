<?php
/**
 * Created by PhpStorm.
 * User: m.korolchuk
 * Date: 9.7.18
 * Time: 14.24
 */

class Itransition_Insurance_Model_Sales_Creditmemo extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract {

    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo) {
        $order = $creditmemo->getOrder();
        $amount = $order->getShippingAddress()->getInsurance();
        if ($amount) {
            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $amount);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $amount);
        }

        return $this;
    }
}