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

    public function setInsuranceEstimate(Varien_Event_Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }

        try {
            //For cart page, update estimate
            $address = $observer->getQuoteAddress();
            $request = $this->getRequest($observer);

            if ($request->getActionName() == self::ACTION_NAME_ESTIMATE
                && $address->getAddressType() == Mage_Customer_Model_Address::TYPE_SHIPPING) {
                $insurance = $request->get(self::REQUEST_PARAM_NAME, 0);
                $this->_helper->setInsuranceToAddress($address, $insurance);
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

    protected function getRequest(Varien_Event_Observer $observer)
    {
        $request = $observer->getRequest();
        if (is_null($request)) {
            $request = Mage::app()->getRequest();
        }

        return $request;
    }
}