<?php

namespace Radarsofthouse\Reepay\Observer;

/**
 * Class QuoteSubmitBefore observer 'sales_model_service_quote_submit_before' event
 *
 * @package Radarsofthouse\Reepay\Observer
 */
class QuoteSubmitBefore implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * sales_model_service_quote_submit_before observer
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
