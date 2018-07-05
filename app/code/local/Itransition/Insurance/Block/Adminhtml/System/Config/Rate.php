<?php
/**
 * Created by PhpStorm.
 * User: m.korolchuk
 * Date: 5.7.18
 * Time: 18.32
 */

class Itransition_Insurance_Block_Adminhtml_System_Config_Rate extends Mage_Adminhtml_Block_System_Config_Form_Field {
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        /** @var Itransition_Insurance_Model_Shipping $shipping**/
        $shipping = Mage::getModel('it_insurance/shipping');
        $shipping_methods = $shipping->getActiveMethods();

        $html = '<div class="itSystemConfigInsurance">';
        foreach ($shipping_methods as $shipping_method) {
            $html .= $this->_getRow($shipping_method);
        }
        $html .= '</div>';

        return $html;
    }

    private function _getRow($method){
        $row = '';
        if(!empty($method['value'])) {
            $row = '<div class="itSystemConfigInsurance__row">';
            $row .= '<div class="itSystemConfigInsurance__titleCarrier">'.$method['label'].'</div>';
            $row .= '<div class="itSystemConfigInsurance__shippingMethods">';
            foreach ($method['value'] as $item) {
                $row .= '<div class="itSystemConfigInsurance__titleShippingMethod">'.$item['label'].'</div>';
                $row .= '<label for="" class="itSystemConfigInsurance__state">'.Mage::helper('adminhtml')->__('Enabled').'<select>';
                $row .= '<option value="0">'.Mage::helper('adminhtml')->__('no').'</option>';
                $row .= '<option value="1">'.Mage::helper('adminhtml')->__('yes').'</option></select></label>';
                $row .= '<label for="" class="itSystemConfigInsurance__state">'.Mage::helper('adminhtml')->__('Percent?').'<select>';
                $row .= '<option value="0">'.Mage::helper('adminhtml')->__('no').'</option>';
                $row .= '<option value="1">'.Mage::helper('adminhtml')->__('yes').'</option></select></label>';
                $row .= '<label for="" class="itSystemConfigInsurance__value">'.Mage::helper('adminhtml')->__('Rate');
                $row .= '<input type="text" name="" value=""></label>';
                $row .= '<input type="hidden" name="" value="'.$item['value'].'">';
            }
            $row .= '</div></div>';
        }

        return $row;
    }
}