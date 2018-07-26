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

    public function saveInsurance(Varien_Event_Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }

        try {
            $address = $observer->getQuoteAddress();
            $request = $this->getRequest($observer);

            if ($address->getAddressType() == Mage_Customer_Model_Address::TYPE_SHIPPING) {
                //For update total on cart page(estimate)
                if ($request->getActionName() == self::ACTION_NAME_ESTIMATE) {
                    $insurance = $request->get(self::REQUEST_PARAM_NAME, 0);
                    $this->_helper->setInsuranceToAddress($address, $insurance);
                }

                $insuranceModel = Mage::getModel('itransition_insurance/insurance');
                $insurance = (float)$address->getInsurance();
                if ($insurance) {
                    $insuranceModel->setInsuranceId($address->getInsuranceId());
                    $insuranceModel->setQuoteAddress($address->getId());
                    $insuranceModel->setInsurance((float)$address->getInsurance());
                    $insuranceModel->setBaseInsurance((float)$address->getBaseInsurance());
                    $insuranceModel->save();
                } else {
                    if ($address->getInsuranceId()) {
                        $insuranceModel->setInsuranceId($address->getInsuranceId());
                        $insuranceModel->delete();
                    }
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    public function setInsuranceFromQuoteToOrderAddress(Varien_Event_Observer $observer)
    {
        try {
            $order = $observer->getOrder();
            $quote = $order->getQuote();
            if ($order && $quote) {
                $orderAddress = $order->getShippingAddress();
                $quoteAddress = $quote->getShippingAddress();

                $insuranceModel = Mage::getModel('itransition_insurance/insurance');
                $insurance = (float)$quoteAddress->getInsurance();

                if (!$insurance) {
                    $insuranceModel->load($quoteAddress->getId(), 'quote_address');
                    $insurance = (float)$insuranceModel->getInsurance();
                }

                $insuranceId = $quoteAddress->getInsuranceId() ?? $insuranceModel->getId();

                if ($insuranceId && $insurance) {
                    $insuranceModel->setInsuranceId($insuranceId);
                    $insuranceModel->setOrderAddress($orderAddress->getId());
                    $insuranceModel->save();
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    public function setInsuranceToQuoteAddress(Varien_Event_Observer $observer)
    {
        return $this->setInsuranceToAddress($observer, 'quote');
    }

    public function setInsuranceToOrderAddress(Varien_Event_Observer $observer)
    {
        return $this->setInsuranceToAddress($observer, 'order');
    }

    protected function setInsuranceToAddress(Varien_Event_Observer $observer, $type)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }

        $method = 'get' . ucfirst($type) . 'AddressCollection';
        $addressCollection = $observer->$method();
        $insuranceTable = Mage::getSingleton('core/resource')->getTableName('itransition_insurance/insurance');
        $field = 'address_id';
        if ($type == 'order') {
            $field = 'entity_id';
        }
        $from = $addressCollection->getSelect()->getPart('from');
        if (!isset($from['insurance_' . $type])) {
            $addressCollection->getSelect()->joinLeft(
                ['insurance_' . $type => $insuranceTable],
                'main_table.' . $field . ' = insurance_' . $type . '.' . $type . '_address',
                ['insurance' => 'insurance', 'base_insurance' => 'base_insurance', 'insurance_id' => 'insurance_id']
            );
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
                $this->_helper->setInsuranceToAddress($address, $insurance);
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