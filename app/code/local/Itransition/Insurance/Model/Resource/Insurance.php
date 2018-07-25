<?php

class Itransition_Insurance_Model_Resource_Insurance extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('itransition_insurance/insurance', 'insurance_id');
    }
}