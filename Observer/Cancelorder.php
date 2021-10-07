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
    protected $logger;

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
        \Radarsofthouse\Reepay\Helper\Data $reepayHelper,
        \Radarsofthouse\Reepay\Helper\Logger $logger
    ) {
        $this->reepayCharge = $reepayCharge;
        $this->scopeConfig = $scopeConfig;
        $this->reepaySession = $reepaySession;
        $this->reepayHelper = $reepayHelper;
        $this->logger = $logger;
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

            $this->logger->addDebug(__METHOD__, ['order ID : ' . $order->getIncrementId()]);

            $apiKey = $this->reepayHelper->getApiKey($order->getStoreId());

            $charge = $this->reepayCharge->get(  
                $apiKey,
                $order->getIncrementId()
            );

            if ($charge['state'] == 'created') {

                $charge = $this->reepayCharge->delete(
                    $apiKey,
                    $order->getIncrementId()
                );

                $this->logger->addDebug("Delete charge for order #".$order->getIncrementId());
            }else{

                $cancelRes = $this->reepayCharge->cancel(
                    $apiKey,
                    $order->getIncrementId()
                );

                $this->logger->addDebug("Cancel charge for order #".$order->getIncrementId());
    
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
            
        }

        return $this;
    }
}
