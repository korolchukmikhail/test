<?php

class Itransition_Insurance_Model_Shipping extends Mage_Core_Model_Abstract
{

    public function getCarriers()
    {
        $carriers = Mage::getSingleton('shipping/config')->getActiveCarriers();
        $options = [];

        foreach ($carriers as $carrierCode => $carrier) {
            $methodOptions = [];
            if ($methods = $carrier->getAllowedMethods()) {
                foreach ($methods as $methodCode => $methodLabel) {
                    $fullMethodCode = $carrierCode . '_' . $methodCode;
                    $methodOptions[] = ['value' => $fullMethodCode, 'label' => $methodLabel];
                }

                if (!$carrierLabel = Mage::getStoreConfig("carriers/$carrierCode/title"))
                    $carrierLabel = $carrierCode;

                $options[] = ['value' => $methodOptions, 'label' => $carrierLabel];
            }
        }

        return $options;
    }

    public function getRates()
    {
        $rates = [];
        if ($ratesSource = Mage::getStoreConfig('insurance/config/rates')) {
            $ratesSource = unserialize($ratesSource);
            foreach ($ratesSource['value'] as $i => $methodCode) {
                $rates[$methodCode] = [
                    'state' => (int)$ratesSource['state'][$i],
                    'percent' => (float)$ratesSource['percent'][$i],
                    'rate' => (float)$ratesSource['rate'][$i],
                ];
            }
        }

        return $rates;
    }
}