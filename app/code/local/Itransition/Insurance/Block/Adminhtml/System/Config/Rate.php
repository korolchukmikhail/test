<?php
/**
 * Created by PhpStorm.
 * User: m.korolchuk
 * Date: 5.7.18
 * Time: 18.32
 */

class Itransition_Insurance_Block_Adminhtml_System_Config_Rate extends Mage_Adminhtml_Block_System_Config_Form_Field {
    protected $_rates = [];

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $this->setElement($element);

        /** @var Itransition_Insurance_Model_Shipping $shipping * */
        $shipping = Mage::getModel('it_insurance/shipping');
        $this->_rates = $shipping->getRates();
        $shipping_carriers = $shipping->getCarriers();

        $html = '<div class="itSystemConfigInsurance">';
        foreach ($shipping_carriers as $shipping_carrier) {
            $html .= $this->_getMethods($shipping_carrier);
        }
        $html .= '</div>';

        return $html;
    }

    private function _getMethods($carrier) {
        $row = '';
        if (!empty($carrier['value'])) {
            $row = '<div class="itSystemConfigInsurance__row">';
            $row .= '<div class="itSystemConfigInsurance__titleCarrier">' . $carrier['label'] . '</div>';
            $row .= '<table class="itSystemConfigInsurance__shippingMethods"><tbody>';
            foreach ($carrier['value'] as $method) {
                $row .= '<tr>';
                $row .= '<td class="itSystemConfigInsurance__titleShippingMethod">' . $method['label'] . '</td>';
                $row .= '<td><label><span>' . Mage::helper('adminhtml')->__('Enabled') . '</span><select name="' . $this->getElement()->getName() . '[state][]" class="itSystemConfigInsurance__select">';
                $row .= '<option value="0" ' . ($this->_getValue($method['value'], 'state') ? '' : 'selected') . '>' . Mage::helper('adminhtml')->__('No') . '</option>';
                $row .= '<option value="1" ' . ($this->_getValue($method['value'], 'state') ? 'selected' : '') . '>' . Mage::helper('adminhtml')->__('Yes') . '</option></select></label></td>';
                $row .= '<td><label><span>' . Mage::helper('adminhtml')->__('Percent?') . '</span><select name="' . $this->getElement()->getName() . '[percent][]" class="itSystemConfigInsurance__select itSystemConfigInsurance__select-percent">';
                $row .= '<option value="0" ' . ($this->_getValue($method['value'], 'percent') ? '' : 'selected') . '>' . Mage::helper('adminhtml')->__('No') . '</option>';
                $row .= '<option value="1" ' . ($this->_getValue($method['value'], 'percent') ? 'selected' : '') . '>' . Mage::helper('adminhtml')->__('Yes') . '</option></select></label></td>';
                $row .= '<td><label><span>' . Mage::helper('adminhtml')->__('Rate');
                $row .= '</span><input type="text" name="' . $this->getElement()->getName() . '[rate][]" class="itSystemConfigInsurance__input" value="' . $this->_getValue($method['value'], 'rate') . '"></label>';
                $row .= '<input type="hidden" name="' . $this->getElement()->getName() . '[value][]" value="' . $method['value'] . '"></td>';
                $row .= '</tr>';
            }
            $row .= '</tbody></table></div>';
        }

        return $row;
    }

    private function _getValue($method_code, $field) {
        if ($this->_rates && isset($this->_rates[$method_code]) && isset($this->_rates[$method_code][$field])) {
            return $this->_rates[$method_code][$field];
        }

        return 0;
    }
}