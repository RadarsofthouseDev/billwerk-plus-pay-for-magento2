<?php

namespace Radarsofthouse\Reepay\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface;

class Reepayvippsepayment extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_vippsepayment';

    /**
     *  Is available only 3 currency DKK EUR NOK.
     *
     * @param CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        if(null !== $quote) {
            $currencyCode = $quote->getCurrency()->getQuoteCurrencyCode();
            if(class_exists('\Radarsofthouse\BillwerkPlusSubscription\Helper\Data')){
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $paymentHelper = $objectManager->get('\Radarsofthouse\BillwerkPlusSubscription\Helper\Data');
                foreach ($quote->getAllItems() as $item) {
                    $product = $item->getProduct();
                    if ($paymentHelper->isBillwerkSubscriptionProduct($product)) {
                        return false;
                    }
                }
            }
        } else {
            $currencyCode = $this->getCurrencyCode();
        }
        if(in_array($currencyCode, ['DKK', 'EUR', 'NOK'])) {
            return true;
        }
        return false;
    }

    /**
     *  Get currency code from current store.
     *
     * @return string|null
     */
    private function getCurrencyCode()
    {
        //        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/test-vipps-epayment.log');
        //        $logger = new \Zend_Log();
        //        $logger->addWriter($writer);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
        $storeId = $this->getStore();
        //        $logger->info('$storeId' . "'$storeId");
        try {
            return $storeManager->getStore($storeId)->getCurrentCurrencyCode();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Get payment method title.
     *
     * @return mixed|string
     */
    public function getTitle()
    {
        $currencyCode = $this->getCurrencyCode();
        if($currencyCode == 'NOK') {
            return $this->getConfigData('title_nok');
        } elseif (in_array($currencyCode, ['DKK', 'EUR'])) {
            return $this->getConfigData('title_dkk_eur');
        }
        return $this->getConfigData('title');
    }
}
