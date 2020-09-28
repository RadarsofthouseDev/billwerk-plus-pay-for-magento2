<?php

namespace Radarsofthouse\Reepay\Observer;

/**
 * Class Cancelorder
 *
 * @package Radarsofthouse\Reepay\Observer
 */
class Cancelorder implements \Magento\Framework\Event\ObserverInterface
{
    private $reepayCharge;
    private $reepaySession;
    protected $scopeConfig;
    protected $reepayHelper;

    /**
     * Constructor
     *
     * @param \Radarsofthouse\Reepay\Helper\Charge $reepayCharge
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Radarsofthouse\Reepay\Helper\Session $reepaySession
     * @param \Radarsofthouse\Reepay\Helper\Data $reepayHelper
     */
    public function __construct(
        \Radarsofthouse\Reepay\Helper\Charge $reepayCharge,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Radarsofthouse\Reepay\Helper\Session $reepaySession,
        \Radarsofthouse\Reepay\Helper\Data $reepayHelper
    ) {
        $this->reepayCharge = $reepayCharge;
        $this->scopeConfig = $scopeConfig;
        $this->reepaySession = $reepaySession;
        $this->reepayHelper = $reepayHelper;
    }

    /**
     * order_cancel_after observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getData('order');

        $paymentMethod = $order->getPayment()->getMethodInstance()->getCode();
        if ($this->reepayHelper->isReepayPaymentMethod($paymentMethod)) {

            $apiKey = $this->reepayHelper->getApiKey($order->getStoreId());

            $cancelRes = $this->reepayCharge->cancel(
                $apiKey,
                $order->getIncrementId()
            );

            if (!empty($cancelRes)) {
                if ($cancelRes['state'] == 'cancelled') {
                    $_payment = $order->getPayment();
                    $this->reepayHelper->setReepayPaymentState($_payment, 'cancelled');

                    // delete reepay session
                    $sessionRes = $this->reepaySession->delete(
                        $apiKey,
                        $order->getIncrementId()
                    );
                }
            }
        }

        return $this;
    }
}
