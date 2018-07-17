<?php

class Itransition_Insurance_Block_Adminhtml_System_Config_Rate extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected $_rates = [];
    /** @var Itransition_Insurance_Model_Shipping $_shippingModel**/
    protected $_shippingModel;
    protected $_template = 'itransition/insurance/system/config/rate.phtml';

    /**
     * @inheritdoc
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $shippingModel = $this->getShippingModel();
        $this->_rates = $shippingModel->getRates();

        return $this->_toHtml();
    }

    protected function _toHtml()
    {
        return parent::_toHtml();
    }

    protected function getShippingModel()
    {
        if (is_null($this->_shippingModel)) {
            $this->_shippingModel = Mage::getModel('itransition_insurance/shipping');
        }

        return $this->_shippingModel;
    }

    public function getCarriers()
    {
        return $this->getShippingModel()->getCarriers();
    }

    public function getValue($method_code, $field)
    {
        if ($this->_rates && isset($this->_rates[$method_code], $this->_rates[$method_code][$field])) {
            return $this->_rates[$method_code][$field];
        }

        return 0;
    }
}