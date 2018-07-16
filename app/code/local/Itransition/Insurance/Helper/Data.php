<?php

class Itransition_Insurance_Helper_Data extends Mage_Core_Helper_Data
{

    public function isEnabled()
    {
        return (bool)Mage::getStoreConfig('insurance/config/enabled');
    }
}