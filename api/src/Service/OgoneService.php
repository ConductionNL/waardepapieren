<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class OgoneService
{

    public function __construct
    (
        ParameterBagInterface $params,
        Request $request
    )
    {
        $this->params = $params;
        $this->request = $request;
    }

    /**
     * @return mixed
     */
    public function canWeHash($arrayOfVars) {

        foreach ($arrayOfVars as $var) {
            if (!isset($var)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $returnedParams
     * @param $shaSignature
     * @return string
     */
    public function verifyReturnedHash($returnedParams, $shaSignature)
    {
        $keyArray = [];
        foreach ($returnedParams as $key => $value) {
            // Dont take the hashed shasign
            if ($key !== 'SHASIGN') {
                $keyArray[strtoupper($key)] = $value;
            }
        }
        ksort($keyArray);

        $signature = [];
        foreach ($keyArray as $key => $value) {
            $signature[] = $key . '=' . $value . $shaSignature;
        }
        $hashedSign = strtoupper(hash('sha256', implode('', $signature)));

        if ($hashedSign === $this->request->query->get('SHASIGN')) {
            return 'success';
        } else {
            return 'failed';
        }
    }

    /**
     * @param array $paramsArray
     * @param string $orderId
     * @param string $returnedOrderId
     * @param array|null $otherReqVars
     * @return string
     */
    public function checkPaymentStatus(array $paramsArray, string $orderId, string $returnedOrderId, array $otherReqVars = null)
    {
        if (isset($paramsArray['STATUS']) && ($paramsArray['STATUS'] == '5' ||
                $paramsArray['STATUS'] == '9' || $paramsArray['STATUS'] == '51' ||
                $paramsArray['STATUS'] == '91') && isset($orderId) && isset($returnedOrderId) && $orderId == $returnedOrderId) {

            if (isset($otherReqVars)) {
                foreach ($otherReqVars as $reqVar) {
                    if (!isset($reqVar)) {
                        return 'invalid';
                    }
                }
            }

            return 'valid';

        } else {
            return 'invalid';
        }
    }

    public function createShaSignature(array $paymentArray, string $shaSignature) {
        $variables['keyArray'] = [];
        foreach ($variables['paymentArray'] as $key => $value) {
            $variables['keyArray'][strtoupper($key)] = $value;
        }
        ksort($variables['keyArray']);

        $variables['signature'] = [];
        foreach ($variables['keyArray'] as $key => $value) {
            $variables['signature'][] = $key . '=' . $value . $shaSignature;
        }

        return hash('sha256', implode('', $variables['signature']));

    }
}
