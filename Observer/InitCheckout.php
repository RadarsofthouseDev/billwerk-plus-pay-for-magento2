<?php

namespace Radarsofthouse\Reepay\Observer;

class InitCheckout implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Radarsofthouse\Reepay\Helper\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Radarsofthouse\Reepay\Helper\Data
     */
    protected $reepayHelper;

    /**
     * Constructor
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Radarsofthouse\Reepay\Helper\Data $reepayHelper
     * @param \Radarsofthouse\Reepay\Helper\Logger $logger
     */
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
     * Observe controller_action_predispatch_checkout_index_index
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->checkoutSession->getLastRealOrder()) {
            $lastRealOrder = $this->checkoutSession->getLastRealOrder();
            if ($lastRealOrder->getPayment()) {
                $paymentMethod = $lastRealOrder->getPayment()->getMethodInstance()->getCode();
                if ($this->reepayHelper->isReepayPaymentMethod($paymentMethod)) {
                    $quoteItems = $this->checkoutSession->getQuote()->getAllVisibleItems();

                    if (count($quoteItems) == 0) {
                        $this->logger->addDebug("restore the last order : ".$lastRealOrder->getEntityId());
                        $this->checkoutSession->restoreQuote();
                    }
                }
            }
        }
    }
}
