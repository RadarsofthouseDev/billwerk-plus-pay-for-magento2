<?php

namespace Radarsofthouse\Reepay\Observer;

/**
 * Class SalesOrderPaymentPlaceStart
 *
 * @package Radarsofthouse\Reepay\Observer
 */
class SalesOrderPaymentPlaceStart implements \Magento\Framework\Event\ObserverInterface
{

    protected $reepayHelper;

    public function __construct(
        \Radarsofthouse\Reepay\Helper\Data $reepayHelper
    ) {
        $this->reepayHelper = $reepayHelper;
    }

    /**
     * sales_order_payment_place_start observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $payment = $observer->getPayment();

        if ( $payment->getMethod() == 'reepay_payment' 
            || $payment->getMethod() == 'reepay_viabill'
            || $payment->getMethod() == 'reepay_mobilepay'
            || $payment->getMethod() == 'reepay_applepay'
            || $payment->getMethod() == 'reepay_paypal'
            || $payment->getMethod() == 'reepay_klarnapaynow'
            || $payment->getMethod() == 'reepay_klarnapaylater'
            || $payment->getMethod() == 'reepay_swish'
            || $payment->getMethod() == 'reepay_resurs'
            || $payment->getMethod() == 'reepay_forbrugsforeningen'
        ) {
            $order = $payment->getOrder();

            if( $this->reepayHelper->getConfig('send_order_email_when_success', $order->getStoreId() ) ){
                $order->setCanSendNewEmailFlag(false)
                    ->setIsCustomerNotified(false)
                    ->save();
            }
            
        }
    }
}
