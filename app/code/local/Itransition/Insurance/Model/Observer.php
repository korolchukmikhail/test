<?php

class Itransition_Insurance_Model_Observer
{
    /** @var Itransition_Insurance_Helper_Data $_helper * */
    protected $_helper;
    const ACTION_NAME_ESTIMATE = 'estimateUpdatePost';
    const REQUEST_PARAM_NAME = 'shipping_method_insurance';

    public function __construct()
    {
        $this->_helper = Mage::helper('itransition_insurance');
    }

    public function unsInsuranceInBilling(Varien_Event_Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }

        $target = $observer->getTarget();
        if ($target && $target->getAddressType() == Mage_Customer_Model_Address::TYPE_BILLING) {
            /**
             * Unset new field in billing address for fix null value on OPC with single shipping address
             * This filed is set to NULL
             * app/code/core/Mage/Checkout/Model/Type/Onepage.php 352 line
             */
            $target->unsetData('insurance');
        }

        return $this;
    }

    public function saveInsurance(Varien_Event_Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }

        try {
            $address = $observer->getQuoteAddress();
            $request = $this->getRequest($observer);

            if ($request->getActionName() == self::ACTION_NAME_ESTIMATE
                && $address->getAddressType() == Mage_Customer_Model_Address::TYPE_SHIPPING) {
                $insurance = $request->get(self::REQUEST_PARAM_NAME, 0);
                $this->_helper->setInsuranceToAddress($address, $insurance);
            }

            if (!is_null($address->getInsurance())) {
                $insuranceModel = Mage::getModel('itransition_insurance/insurance')->load($address->getId(), 'quote_address');
                $insurance = (float)$address->getInsurance();
                if ($insurance) {
                    if ($insuranceModel->getIsnurance() != $insurance) {
                        $insuranceModel->setQuoteAddress($address->getId());
                        $insuranceModel->setInsurance((float)$address->getInsurance());
                        $insuranceModel->setBaseInsurance((float)$address->getBaseInsurance());
                        $insuranceModel->save();
                    }
                } else {
                    if ($insuranceModel->getId()) {
                        $insuranceModel->delete();
                    }
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    public function setInsuranceToAddress(Varien_Event_Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }

        $addressCollection = $observer->getQuoteAddressCollection();

        try {
            foreach ($addressCollection as $address) {
                if ($address->getAddressType() == Mage_Customer_Model_Address::TYPE_SHIPPING) { //TODO: NEED OPTIMIZATION
                    $insuranceModel = Mage::getModel('itransition_insurance/insurance')->load($address->getId(), 'quote_address');
                    if (!is_null($insuranceModel->getInsurance())) {
                        $address->setInsurance($insuranceModel->getInsurance());
                        $address->setBaseInsurance($insuranceModel->getBaseInsurance());
                    }
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    public function setInsurance(Varien_Event_Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }

        try {
            $request = $this->getRequest($observer);
            $address = $observer->getQuote()->getShippingAddress();

            $insurance = $request->getPost(self::REQUEST_PARAM_NAME, 0);
            $this->_helper->setInsuranceToAddress($address, $insurance);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    public function setInsuranceOnCreateOrder(Varien_Event_Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }

        try {
            $request = $this->getRequest($observer);
            $orderData = $request->get('order');
            if ($orderData && !isset($orderData['send_confirmation']) &&
                (isset($orderData['shipping_method_insurance']) || isset($orderData['shipping_method']))) {
                $address = $observer->getOrderCreateModel()->getQuote()->getShippingAddress();
                $insurance = isset($orderData['shipping_method_insurance']) ? $orderData['shipping_method_insurance'] : 0;
                $this->_helper->setInsuranceToAddress($address, $insurance, true);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    public function setMultiShippingInsurance(Varien_Event_Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }

        try {
            $request = $this->getRequest($observer);
            $addresses = $observer->getQuote()->getAllShippingAddresses();

            foreach ($addresses as $address) {
                $insurance = $request->getPost(self::REQUEST_PARAM_NAME . '__' . $address->getId(), 0);
                $this->_helper->setInsuranceToAddress($address, $insurance);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    public function prepareItemsPaypal(Varien_Event_Observer $observer)
    {
        $cart = $observer->getPaypalCart();
        $shippingAddress = $cart->getSalesEntity()->getShippingAddress();

        if ($shippingAddress && (float)($amount = $shippingAddress->getInsurance()) > 0) {
            $cart->addItem(Itransition_Insurance_Helper_Data::MAIN_LABEL_INSURANCE, 1, $amount);
        }

        return $this;
    }

    protected function getRequest(Varien_Event_Observer $observer)
    {
        $request = $observer->getRequest();
        if (is_null($request)) {
            $request = Mage::app()->getRequest();
        }

        return $request;
    }
}