<?php

class Itransition_Insurance_Block_Insurance extends Mage_Core_Block_Template
{
    protected $_template = "itransition/insurance/insurance.phtml";
    protected static $_rates;

    public function getRates()
    {
        if (is_null(self::$_rates)) {
            self::$_rates = Mage::getModel('itransition_insurance/shipping')->getRates();
        }
        return self::$_rates;
    }
}