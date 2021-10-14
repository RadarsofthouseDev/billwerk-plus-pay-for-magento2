<?php

namespace Radarsofthouse\Reepay\Observer;

/**
 * Class InitCheckout observer 'controller_action_predispatch_checkout_index_index' event
 *
 * @package Radarsofthouse\Reepay\Observer
 */
class InitCheckout implements \Magento\Framework\Event\ObserverInterface
{

    protected $logger;
    protected $checkoutSession;
    protected $reepayHelper;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Radarsofthouse\Reepay\Helper\Data $reepayHelper,
        \Radarsofthouse\Reepay\Helper\Logger $logger
    ) {
        $this->logger = $logger;
        $this->checkoutSession = $checkoutSession;
        $this->reepayHelper = $reepayHelper;
    }

    /**
     * controller_action_predispatch_checkout_index_index observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if( $this->checkoutSession->getLastRealOrder() ){
            $lastRealOrder = $this->checkoutSession->getLastRealOrder();
            if( $lastRealOrder->getPayment() ){
                $paymentMethod = $lastRealOrder->getPayment()->getMethodInstance()->getCode();
                if ($this->reepayHelper->isReepayPaymentMethod($paymentMethod)) {
                    
                    $quoteItems = $this->checkoutSession->getQuote()->getAllVisibleItems();

                    if( count($quoteItems) == 0 ){
                        $this->logger->addDebug("restore the last order : ".$lastRealOrder->getEntityId());
                        $this->checkoutSession->restoreQuote();
                    }

                }
            }
        }
    }
}
