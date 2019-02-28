<?php

namespace Radarsofthouse\Reepay\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 *
 * @package Radarsofthouse\Reepay\Helper
 */
class Data extends AbstractHelper
{
    const XML_PATH = 'reepay_setting/';

    /**
     * @param $field
     * @param null $storeId
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue($field, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param $code
     * @param null $storeId
     * @return mixed
     */
    public function getApiConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH . $code, $storeId);
    }

    /**
     * set reepay payment state to radarsofthouse_reepay_status
     *
     * @param  $payment
     * @param string $state
     * @return void
     */
    public function setReepayPaymentState($payment, $state)
    {
        // $_additionalData = unserialize($payment->getAdditionalData());
        // $_additionalData['state'] = $state;

        $_additionalInfo = $payment->getAdditionalInformation();
        $_additionalInfo['raw_details_info']['state'] = $state;
        $payment->setAdditionalInformation($_additionalInfo);
        $payment->save();

        
        $orderId = $payment->getOrder()->getIncrementId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $reepayStatusModel = $objectManager->create('Radarsofthouse\Reepay\Model\Status');
        $reepayStatus = $reepayStatusModel->load($orderId, 'order_id');
        if ($reepayStatus->getStatusId()) {
            $reepayStatus->setStatus($state);
            $reepayStatus->save();
        }
    }

    /**
     * update reepay payment data to radarsofthouse_reepay_status
     *
     * @param string $orderId
     * @param array $data
     * @return void
     */
    public function updateReepayPaymentData($orderId, $data)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $reepayStatusModel = $objectManager->create('Radarsofthouse\Reepay\Model\Status');
        $reepayStatus = $reepayStatusModel->load($orderId, 'order_id');
        if ($reepayStatus->getStatusId()) {
            if (!empty($data['status'])) {
                $reepayStatus->setStatus($data['status']);
            }
            if (!empty($data['first_name'])) {
                $reepayStatus->setFirstName($data['first_name']);
            }
            if (!empty($data['last_name'])) {
                $reepayStatus->setLastName($data['last_name']);
            }
            if (!empty($data['email'])) {
                $reepayStatus->setEmail($data['email']);
            }
            if (!empty($data['token'])) {
                $reepayStatus->setToken($data['token']);
            }
            if (!empty($data['masked_card_number'])) {
                $reepayStatus->setMaskedCardNumber($data['masked_card_number']);
            }
            if (!empty($data['fingerprint'])) {
                $reepayStatus->setFingerprint($data['fingerprint']);
            }
            if (!empty($data['card_type'])) {
                $reepayStatus->setCardType($data['card_type']);
            }
            if (!empty($data['error'])) {
                $reepayStatus->setError($data['error']);
            }
            if (!empty($data['error_state'])) {
                $reepayStatus->setErrorState($data['error_state']);
            }

            $reepayStatus->save();
        }
    }

    /**
     * get private api key from cinfiguration
     *
     * @param string $apiKey
     */
    public function getApiKey($store = null)
    {
        if ($store === null) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $storeManagerInterface = $objectManager->create('\Magento\Store\Model\StoreManagerInterface');
            $store = $storeManagerInterface->getStore()->getStoreId();
        }
        
        $apiKey = null;
        $testModeConfig = $this->scopeConfig->getValue('payment/reepay_payment/api_key_type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        if ($testModeConfig == 1) {
            $apiKey = $this->scopeConfig->getValue('payment/reepay_payment/private_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        } else {
            $apiKey = $this->scopeConfig->getValue('payment/reepay_payment/private_key_test', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        }

        return $apiKey;
    }
}
