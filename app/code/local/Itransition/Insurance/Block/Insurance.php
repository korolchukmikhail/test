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

    public function getPrice($rate, $formatted = true)
    {
        $rates = $this->getRates();
        $rawPrice = ($rates[$rate->getCode()]['percent'] ? ($rate->getPrice() * ($rates[$rate->getCode()]['rate'] / 100)) : $rates[$rate->getCode()]['rate']);
        $formattedPrice = $this->getQuote()->getStore()->convertPrice($rawPrice, true);

        return $formatted ? $formattedPrice : $rawPrice;
    }

    public function getQuote() {
        if($this->getData('quote')) {
            return $this->getData('quote');
        }

        if($this->getData('address')) {
            return $this->getData('address')->getQuote();
        }

        return Mage::getSingleton('checkout/session')->getQuote();
    }
}