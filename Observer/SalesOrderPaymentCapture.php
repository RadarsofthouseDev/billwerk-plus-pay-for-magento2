<?php

namespace Radarsofthouse\Reepay\Observer;

/**
 * Class SalesOrderPaymentCapture observer 'sales_order_payment_capture' event
 *
 * @package Radarsofthouse\Reepay\Observer
 */
class SalesOrderPaymentCapture implements \Magento\Framework\Event\ObserverInterface
{

    protected $reepayHelper;
    protected $logger;
    protected $messageManager;
    protected $reepayCharge;

    public function __construct(
        \Radarsofthouse\Reepay\Helper\Data $reepayHelper,
        \Radarsofthouse\Reepay\Helper\Logger $logger,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Radarsofthouse\Reepay\Helper\Charge $reepayCharge
    ) {
        $this->reepayHelper = $reepayHelper;
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->reepayCharge = $reepayCharge;
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
        $invoice = $observer->getInvoice();
        
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
            $amount = $invoice->getGrandTotal();

            $this->logger->addDebug(__METHOD__, ['capture : '.$order->getIncrementId().', amount : '.$amount]);

            $orderInvoices = $order->getInvoiceCollection();

            $options = [];
            $options['key'] = count($orderInvoices);
            $options['amount'] = $amount*100;
            $options['ordertext'] = "settled";

            $apiKey = $this->reepayHelper->getApiKey($order->getStoreId());
        
            $charge = $this->reepayCharge->settle(
                $apiKey,
                $order->getIncrementId(),
                $options
            );

            if (!empty($charge)) {

                if( isset($charge["error"]) ){
                    $this->logger->addDebug("settle error : ",$charge);
                    $this->messageManager->addError($charge["error"]);
                    throw new \Magento\Framework\Exception\LocalizedException($charge["error"]);
                    return;
                }

                if ($charge['state'] == 'settled') {
                    $_payment = $order->getPayment();
                    $this->reepayHelper->setReepayPaymentState($_payment, 'settled');
                    $order->save();

                    $this->logger->addDebug('settled : '.$order->getIncrementId());
                    
                    // separate transactions for partial capture
                    $payment->setIsTransactionClosed(false);
                    $payment->setTransactionId($charge['transaction']);
                    $transactionData = [
                        'handle' => $charge['handle'],
                        'transaction' => $charge['transaction'],
                        'state' => $charge['state'],
                        'amount' => $amount,
                        'customer' => $charge['customer'],
                        'currency' => $charge['currency'],
                        'created' => $charge['created'],
                        'authorized' => $charge['authorized'],
                        'settled' => $charge['settled']
                    ];
                    $payment->setTransactionAdditionalInfo(
                        \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS,
                        $transactionData
                    );

                    $this->logger->addDebug('set capture transaction data');
                }
            } else {
                $this->logger->addDebug("Empty settle response from Reepay");
                $this->messageManager->addError("Empty settle response from Reepay");
                throw new \Magento\Framework\Exception\LocalizedException("Empty settle response from Reepay");
                return;
            }

        }
    }
}
