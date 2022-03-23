<?php

namespace Radarsofthouse\Reepay\Observer;

class QuoteSubmitBefore implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * Observe sales_model_service_quote_submit_before
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getQuote();
        $order = $observer->getOrder();

        $order->setReepayCreditCard($quote->getReepayCreditCard());
    }
}
